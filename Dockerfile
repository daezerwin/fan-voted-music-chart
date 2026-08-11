# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.4

FROM composer:2 AS composer

FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:${PHP_VERSION}-fpm-alpine AS base

RUN set -eux; \
    apk add --no-cache icu-libs libzip oniguruma; \
    apk add --no-cache --virtual .build-deps icu-dev libzip-dev oniguruma-dev $PHPIZE_DEPS; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip bcmath opcache; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    apk del .build-deps

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN addgroup -g 1000 laravel && adduser -G laravel -u 1000 -D laravel

WORKDIR /var/www/html

FROM base AS development

RUN apk add --no-cache nodejs npm bash

USER laravel

CMD ["php-fpm"]

FROM base AS production

COPY --chown=laravel:laravel . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && composer clear-cache

COPY --from=frontend --chown=laravel:laravel /app/public/build ./public/build

RUN chown -R laravel:laravel storage bootstrap/cache

USER laravel

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD php artisan about > /dev/null || exit 1

# RUN_MIGRATIONS=true (set only on the `app` service in
# docker-compose.prod.yml) runs migrations before php-fpm starts. `queue`
# and `scheduler` boot this same image with it unset, so they never race
# `app` to migrate. See docker/php/entrypoint.sh.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# Nginx serves static assets directly and proxies PHP requests to the `app`
# container over FastCGI. It needs its own copy of the whole public/
# directory — not just the compiled assets — because `try_files`/`index`
# resolve against files nginx can see on its own filesystem: without a real
# index.php present, resolving "/" 404s/403s before the request ever reaches
# the fastcgi_pass fallback, even though PHP-FPM never executes this copy.
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public /var/www/html/public
COPY --from=frontend /app/public/build /var/www/html/public/build

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD wget --spider -q http://localhost/ || exit 1
