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
* **Phase 6 — Chart Engine**: daily chart generation, ranking, movement, peak rank, chart history.
* **Phase 7 — YouTube Playback**: embedded player on song pages, Top 10 queue player via the IFrame API.
* **Phase 8 — Admin**: artist/song/genre management, voting activity visibility, manual chart regeneration.
* **Phase 9 — Discovery**: Trending Now, Biggest Gainers, and New Entries on the homepage and genre pages.
* **Phase 10 — Infrastructure & Deployment**: paired app/web GHCR images, a Portainer-ready production
  Compose stack, and CI → Docker → Deploy workflows (deploy is safe-by-default/manual until explicitly
  enabled — see "Production Deployment").

Phase 11 (a dedicated production-hardening review pass) hasn't happened yet. Phase 12 (monetization) is
deliberately not built — the spec itself defers it "until product usage warrants it," and this app has
no real users yet.

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

Production uses two other targets instead: `production` (PHP-FPM — installs Composer dependencies
without dev packages, bundles compiled frontend assets, no Node at runtime) and `web` (Nginx, with its
own copy of `public/` plus the compiled assets, so it never depends on a shared volume or the app
container's filesystem). See "Container Images" and "Portainer Deployment" below.

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

## Chart Generation

```bash
docker compose exec app php artisan charts:generate-daily [date]
```

Tallies votes for `date` (defaults to yesterday, since the chart for "today" reflects a completed
prior day of voting) and snapshots a `Chart` + ordered `ChartEntry` rows. All ranking/tie-break logic
lives in `App\Actions\Charts\CalculateChartRanking` — never duplicate it in a controller, view, or
ad-hoc query. Ranking:

1. Vote count, descending.
2. Ties: earlier time the final vote total was reached.
3. Ties: better previous-day chart rank.
4. Ties: stable song ID, for full determinism.

Generation is idempotent (rerunning for the same date replaces that chart's entries rather than
duplicating them), transactional (a partial chart is never exposed), and guarded by a cache lock so
overlapping scheduler runs can't double-generate. It only ranks songs whose song *and* artist are
currently active. The scheduler runs it daily at 00:15 (`routes/console.php`).

Only daily charts are implemented; `chart_type` supports `weekly` in the schema for a future addition,
but weekly generation doesn't exist yet.

## Discovery

Three sections on the homepage and on each genre page (`App\Actions\Discovery\*`, all optionally
scoped to a genre):

* **Trending Now** — most votes cast *today*, queried live from `votes`. Deliberately independent of
  the official chart, which reflects a completed prior day — this is the only real-time signal on the
  site.
* **Biggest Gainers** — entries from the latest daily chart with the largest positive `movement`.
* **New Entries** — entries from the latest daily chart with no `previous_rank` (covers both brand-new
  and re-entries).

All three degrade to an empty-state message when there's no data yet (e.g. a fresh install with no
votes or no chart generated), rather than a broken or misleadingly empty-looking section.

## YouTube Integration

Individual song pages use a standard official `<iframe>` embed (`youtube.com/embed/{video_id}`) — no
custom API needed for a single video. `/play` plays the current Top 10 as a queue using the real
YouTube IFrame Player API (`resources/js/top-ten-player.js`, an Alpine.js component), auto-advancing
on `ENDED` and skipping straight to the next track on `onError` (unavailable video or embedding
disabled) so one bad video never stalls the queue. `App\Support\Youtube\NormalizeYoutubeVideoId`
extracts a bare video ID from watch/youtu.be/embed/shorts URLs or a raw ID; the admin song form uses
it so admins can paste a full URL instead of hunting for the raw ID.

Site votes are independent first-party data — they are never synced to or confused with YouTube
likes/views, and nothing here downloads video/audio or interacts with YouTube engagement features.

## Admin

Sign in as `admin@example.com` (seeded by `DatabaseSeeder`) to reach `/admin`, gated by the `admin`
route middleware plus a `Gate::before` that grants admins every ability. Available today:

* **Artists / Songs / Genres** — full create/edit, with `is_active` (visibility), `is_featured`, and
  (for songs) `voting_enabled` toggles. Deleting an artist or song is intentionally not exposed —
  deactivate instead, since both have a `restrictOnDelete` foreign key once they're chart/vote history.
  Genres can be deleted, but the database rejects it while any song still references them.
* **Votes** (`/admin/votes`) — a per-day view of vote counts by user and a recent-votes feed. This is
  visibility only; there's no automated fraud scoring or flagging yet (that's real Phase 11 anti-abuse
  work), so it's a starting point for a human to notice unusual vote velocity, not a moderation tool.
* **Charts** (`/admin/charts`) — manually regenerate a specific date's chart snapshot; useful after a
  correction (e.g. deactivating a song) that should be reflected retroactively.
* **Dashboard** — basic counts (users, active artists/songs, genres, votes today).

There's only one role in practice right now (`UserRole::Admin`); `moderator` exists in the schema but
has no distinct permissions yet — introducing that split is deferred until it's actually needed, per
the "avoid an overly complicated role/permission system" guidance. Editorial content management and
submission moderation aren't implemented either: there's no editorial-content model in the spec's core
domain, and users don't submit songs in this product (only admins add catalog entries), so there's
nothing to moderate yet.

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

Three workflows, deliberately separated so PRs never get registry credentials and a failing build can
never reach production:

* **`ci.yml`** — runs on pull requests and pushes to `main`. Installs PHP/Node dependencies, checks
  formatting with Pint, builds frontend assets, runs migrations against a MySQL service container, runs
  the test suite, validates both `docker-compose.yml` and `docker-compose.prod.yml`, and build-only
  (no push) both the `production` and `web` image targets.
* **`docker.yml`** — runs only after `ci.yml` succeeds on `main` (via `workflow_run`), plus on
  `v*.*.*` tags. Builds and pushes the `production` (app) and `web` (Nginx) images to GHCR, tagged
  together from the same commit so they can never mismatch. Needs no repository configuration beyond
  the automatically-provided `GITHUB_TOKEN`.
* **`deploy.yml`** — runs after `docker.yml` succeeds. Safe by default: unless the `DEPLOY_ENABLED`
  repository/environment variable is `"true"`, it just logs that it's skipping and prints what to
  configure. Once enabled, it calls the `PORTAINER_WEBHOOK` secret to trigger a stack update, waits,
  then verifies the `APP_URL` variable's `/up` endpoint — failing loudly (`::error::`) if health checks
  don't pass after deploy, rather than silently reporting success.

## Container Images

Every build produces two images from the same `Dockerfile`, both tagged from the same commit:

```text
ghcr.io/<owner>/<repo>        — production target: Laravel via PHP-FPM (also used by queue/scheduler)
ghcr.io/<owner>/<repo>-web    — web target: Nginx + that exact commit's compiled assets
```

Tags follow `latest`, `main`, `sha-<commit>` on every push to `main`, plus semver tags (`v1.2.0`,
`1.2`, `1`) on version tags. **Always deploy a `sha-*` tag, never bare `latest`** — the immutable SHA
tag is what makes rollback (below) possible.

## Portainer Deployment

Deploy `docker-compose.prod.yml` as a Portainer Stack. It consumes prebuilt images only — nothing is
built on the server. Required stack variables:

| Variable | Example | Notes |
|---|---|---|
| `IMAGE_REPOSITORY` | `ghcr.io/your-org/music-chart` | Without the `-web` suffix; the stack file adds it for the web image. |
| `APP_IMAGE_TAG` | `sha-abc1234` | Never `latest` alone. |
| `APP_PORT` | `8080` | Host port for Nginx. |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | | Also referenced by the `app`/`queue`/`scheduler` containers via `.env`. |
| `REDIS_PASSWORD` | | Redis requires this password in production (`--requirepass`); it must match the app's `.env`. |

Plus a real production `.env` (from `.env.example`) — `APP_ENV=production`, `APP_DEBUG=false`,
a proper `APP_KEY`, `APP_URL`, and the Facebook/YouTube credentials.

If the GHCR images are private, add a registry credential in Portainer (or a `docker login` on the
host) before deploying — otherwise the stack will fail to pull.

## Production Deployment

```text
Merge to main → CI → Docker (build + push to GHCR) → Deploy (Portainer webhook) → verify /up
```

Until `DEPLOY_ENABLED` is turned on, the flow stops after the Docker workflow: images land in GHCR and
an operator updates the Portainer stack's `APP_IMAGE_TAG` manually. This is the spec's explicit
"acceptable interim state" — CI validates, builds, and pushes; automating the last step is a
deliberate, reviewable switch to flip on, not a default.

`app`, `queue`, and `scheduler` always use the same `APP_IMAGE_TAG` — never update one without the
others, or workers will run different code than the web app.

## Rollback

Every deployment is identifiable by its immutable `sha-<commit>` tag. To roll back, change the
Portainer stack's `APP_IMAGE_TAG` variable to the previous known-good SHA and redeploy the stack —
no rebuild required. Run `php artisan migrate:status` after rolling back if the previous deploy
included a migration; a schema rollback is a separate, deliberate decision the tooling here doesn't
automate.

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
