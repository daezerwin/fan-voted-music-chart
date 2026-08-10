# Fan-Voted Music Chart

## About

An independent, community-driven music discovery and ranking platform. Users vote for songs, watch
daily and historical charts move, and play the current Top 10 through the official YouTube embedded
player. Voting, ranking, and chart history are the platform's own first-party data — YouTube is used
only for playback and metadata.

This is not affiliated with, and does not imply endorsement by, Billboard.

See [agents.md](agents.md) for the full product/engineering specification and phased roadmap. This
README documents what is actually implemented today.

## Status

This repository currently implements:

* **Phase 1 — Project Foundation**: Laravel skeleton, Docker environment, Tailwind base layout, CI.
* **Phase 2 — Domain Foundation**: Artist/Genre/Song models, migrations, factories, seeders, admin foundation.
* **Phase 3 — Public Catalog**: homepage, artist/genre/song pages, search.
* **Phase 4 — Authentication**: Facebook sign-in via Laravel Socialite.
* **Phase 5 — Voting**: one vote per user per song per day, enforced by a database unique constraint.

The chart/ranking engine and YouTube playback land in later phases and are not yet implemented.

## Technology Stack

* PHP 8.4, Laravel 13
* Blade + Tailwind CSS 4 (via Vite)
* MySQL 8
* Redis (cache, sessions, queue)
* Docker / Docker Compose
* GitHub Actions

## Architecture

```text
Browser
   |
   v
Nginx
   |
   v
Laravel / PHP-FPM
   |
   +--------+---------+
   |        |         |
Database  Redis     YouTube
             |
        +----+-----+
        |          |
      Queue     Scheduler
```

## Requirements

* Git
* Docker
* Docker Compose

No local PHP, Composer, Node, MySQL, or Redis installation is required — everything runs in containers.

## Local Development

```bash
git clone <repository-url>
cd <repository>

cp .env.example .env

docker compose up -d

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

The app is served by the `web` (Nginx) container at [http://localhost:8080](http://localhost:8080)
(configurable via `APP_PORT`).

For frontend asset changes, run the Vite dev server on the host or inside the container:

```bash
docker compose exec app npm install
docker compose exec app npm run dev
```

Or build production assets once:

```bash
docker compose exec app npm run build
```

## Docker Development

`docker-compose.yml` builds the `development` target of the `Dockerfile`, which includes Composer and
Node so dependency and build commands can run via `docker compose exec app ...`. The application source
is bind-mounted, so code changes are picked up without rebuilding the image.

To also expose the database and Redis ports on the host (useful for GUI clients):

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

Production uses the `production` target instead, which installs Composer dependencies without dev
packages, bundles compiled frontend assets, and does not require Node at runtime. See
[agents.md](agents.md) for the planned production/Portainer deployment flow (Phase 10).

## Environment Variables

| Variable | Purpose |
|---|---|
| `APP_TIMEZONE` | Timezone used to determine the voting/chart day. Configurable per deployment. |
| `DB_*` | Database connection; defaults match the `database` service in `docker-compose.yml`. |
| `REDIS_*` | Redis connection; defaults match the `redis` service in `docker-compose.yml`. |
| `FACEBOOK_CLIENT_ID` / `FACEBOOK_CLIENT_SECRET` / `FACEBOOK_REDIRECT_URI` | Laravel Socialite Facebook OAuth credentials. See Authentication below. |
| `YOUTUBE_API_KEY` | YouTube Data API key for metadata refresh. Not yet wired up (Phase 7). |
| `APP_IMAGE_TAG` | Image tag deployed by production Compose/Portainer. Not used in local development. |

Never commit a real `.env` file or production secrets.

## Database Setup

The `database` service (MySQL 8) is created automatically by Docker Compose. Run migrations with:

```bash
docker compose exec app php artisan migrate
```

## Authentication

Sign-in is Facebook-only via [Laravel Socialite](https://laravel.com/docs/socialite), reachable at
`/auth/facebook/redirect` and `/auth/facebook/callback`. The provider→user mapping lives in a
dedicated `user_identities` table (not on `users` directly) so additional providers (Google, Apple)
can be added later without changing the user schema. If Facebook doesn't return an email, a
placeholder (`facebook-{id}@no-email.invalid`) is stored and `email_verified_at` is left null.

To enable real sign-in, create a Facebook app and set:

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=http://localhost:8080/auth/facebook/callback
```

Without these, the redirect still works (it builds a valid Facebook OAuth URL) but Facebook will
reject the sign-in attempt.

## Voting System

A signed-in user may vote once per song per calendar day, determined by `APP_TIMEZONE` (defaults to
UTC — set it to your target market's timezone, e.g. `Asia/Manila`). Duplicate prevention is enforced
by a database unique constraint on `(user_id, song_id, vote_date)`, not a check-then-insert, so
concurrent double-submits can never create two valid votes; the `App\Actions\Voting\CastVote` action
catches the resulting unique-constraint violation and reports it as "already voted" instead of
erroring. Votes are also rate-limited per user (`votes` limiter, 20/minute) via `throttle:votes`.

There is no chart/ranking engine yet (Phase 6), so the song page currently shows only today's raw
vote count, not a chart position.

## Testing

```bash
docker compose exec app php artisan test
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`) so they don't require the
`database` container to be healthy.

## Code Quality

```bash
docker compose exec app ./vendor/bin/pint --test
```

Static analysis (Larastan/PHPStan) is not configured yet; it will be introduced when it earns its keep.

## Common Commands

```bash
# Start
docker compose up -d

# Stop
docker compose down

# Logs
docker compose logs -f

# Laravel
docker compose exec app php artisan about

# Migrate
docker compose exec app php artisan migrate

# Tests
docker compose exec app php artisan test

# Tinker
docker compose exec app php artisan tinker

# Queue worker logs
docker compose logs -f queue

# Scheduler logs
docker compose logs -f scheduler
```

## Docker Services

| Service | Purpose |
|---|---|
| `web` | Nginx, serves the app and proxies PHP requests to `app`. |
| `app` | Laravel via PHP-FPM. |
| `database` | MySQL 8. |
| `redis` | Cache, sessions, queue transport. |
| `queue` | `php artisan queue:work`, same image as `app`. |
| `scheduler` | `php artisan schedule:work`, same image as `app`. |

## GitHub Actions

`.github/workflows/ci.yml` runs on pull requests and pushes to `main`:

* Installs PHP and Node dependencies.
* Checks formatting with Pint.
* Builds frontend assets.
* Runs migrations against a MySQL service container.
* Runs the test suite.
* Validates `docker compose config` and builds the production Docker image.

No image publishing or deployment workflow exists yet — that lands in Phase 10.

## Troubleshooting

* **`docker compose exec app ...` fails with "no such service"** — run `docker compose up -d` first.
* **Permission errors under `storage/` or `bootstrap/cache/`** — these paths must be writable by the
  container's `laravel` user (uid 1000); avoid `chmod -R 777`.
* **Assets not updating** — rebuild with `docker compose exec app npm run build`, or run `npm run dev`
  for live reload.

## Security

See the Security section of [agents.md](agents.md). Report concerns to the maintainers rather than
filing a public issue if the concern is exploitable.

## License

Not yet decided.
