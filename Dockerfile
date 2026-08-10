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

CMD ["php-fpm"]
