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
* **Phase 10 — Infrastructure & Deployment**: paired app/web GHCR images published on version-tag push,
  a Portainer-ready production Compose stack with automatic (idempotent) migrations on the `app`
  service only, and a manual, operator-triggered "Pull and redeploy" deployment model — see "Production
  Deployment".
* **Phase 11 — Production Hardening**: a review pass across caching, concurrency, security, SEO, and
  accessibility — see "Production Hardening Review" for exactly what changed and what's deliberately
  deferred.

Phase 12 (monetization) is deliberately not built — the spec itself defers it "until product usage
warrants it," and this app has no real users yet.

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

Two ways to sign in: Facebook via [Laravel Socialite](https://laravel.com/docs/socialite), and manual
email/password registration.

**Facebook** is reachable at `/auth/facebook/redirect` and `/auth/facebook/callback`. The
provider→user mapping lives in a dedicated `user_identities` table (not on `users` directly) so
additional providers (Google, Apple) can be added later without changing the user schema. If Facebook
doesn't return an email, a placeholder (`facebook-{id}@no-email.invalid`) is stored and
`email_verified_at` is left null.

To enable real Facebook sign-in, create a Facebook app and set:

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=http://localhost:8080/auth/facebook/callback
```

Without these, the redirect still works (it builds a valid Facebook OAuth URL) but Facebook will
reject the sign-in attempt.

**Manual registration** (`/register`, `/login`) creates a user with a hashed `password` (the `users`
table column added for this — it's nullable, since Facebook-only accounts never set one). A user who
registered manually and later signs in with Facebook using the same email gets the identity linked to
their existing account rather than a duplicate one, via the same account-linking logic Facebook login
already uses. Login is rate-limited (5 attempts per email+IP, standard Laravel throttling) and both
`/register` and `/login` redirect an already-authenticated user to the homepage rather than showing the
form again. There's no email verification or password-reset flow yet — `email_verified_at` stays null
for manually-registered accounts, and nothing in the app currently gates on it.

## Voting System

A signed-in user may vote once per song per calendar day, determined by `APP_TIMEZONE` (defaults to
UTC — set it to your target market's timezone, e.g. `Asia/Manila`). Duplicate prevention is enforced
by a database unique constraint on `(user_id, song_id, vote_date)`, not a check-then-insert, so
concurrent double-submits can never create two valid votes; the `App\Actions\Voting\CastVote` action
catches the resulting unique-constraint violation and reports it as "already voted" instead of
erroring. Votes are also rate-limited per user (`votes` limiter, 20/minute) via `throttle:votes`. Each
vote records the casting IP address for audit visibility (see "Admin" and "Production Hardening
Review") — it's never used to block or throttle a vote by itself.

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

`/admin` is gated by the `admin` route middleware plus a `Gate::before` that grants admins every
ability.

**Local dev**: sign in as `admin@example.com` (seeded by `DatabaseSeeder` via `php artisan db:seed`).

**Production**: there's no seeded admin — `DatabaseSeeder`'s `ArtistSeeder`/`SongSeeder` use Faker to
generate fake demo data, and Faker is a dev-only Composer dependency deliberately excluded from the
production image (`composer install --no-dev`), so those seeders can't and shouldn't run there. Bootstrap
the first admin instead:

1. Register a real account at `/register` (or sign in with Facebook) on the live site.
2. From the host, promote it:
   ```bash
   docker compose exec app php artisan users:promote-admin you@example.com
   ```
3. Sign in again (or refresh) — you now have `/admin` access.

`GenreSeeder` has no Faker dependency (it's a fixed curated list), so it's safe to run in production
too if you want the 10 starter genres instead of creating them by hand:
```bash
docker compose exec app php artisan db:seed --class=GenreSeeder --force
```

To populate the catalog from the admin UI, create **genres and artists first** — the song form requires
picking an existing artist and genre, so there's nothing to attach a song to until those exist:
`/admin/genres` → `/admin/artists` → `/admin/songs` (paste a full YouTube URL or a bare video ID; it's
normalized and validated for uniqueness automatically).

Available today:

* **Artists / Songs / Genres** — full create/edit, with `is_active` (visibility), `is_featured`, and
  (for songs) `voting_enabled` toggles. Deleting an artist or song is intentionally not exposed —
  deactivate instead, since both have a `restrictOnDelete` foreign key once they're chart/vote history.
  Genres can be deleted, but the database rejects it while any song still references them.
* **Votes** (`/admin/votes`) — a per-day view of vote counts by user, IPs shared by more than one
  account, and a recent-votes feed (with IP). This is visibility only; there's no automated fraud
  scoring or flagging, so it's a starting point for a human to notice unusual patterns, not a
  moderation tool — shared IPs in particular are normal (households, NAT, campus networks) and aren't
  evidence of abuse by themselves.
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

Two workflows, independent of each other:

* **`ci.yml`** — an optional development safety net on pull requests and pushes to `main`. Installs
  PHP/Node dependencies, checks formatting with Pint, builds frontend assets, runs migrations against a
  MySQL service container, runs the test suite, validates both `docker-compose.yml` and
  `docker-compose.prod.yml`, and build-only (no push) both the `production` and `web` image targets. It
  does not gate or trigger publishing — pushing a release tag is what publishes an image, regardless of
  `ci.yml`'s state.
* **`docker-publish.yml`** — the actual release action. Triggers only on a pushed `v*.*.*` tag or a
  manual `workflow_dispatch` (with a `tag` input for re-publishing an old release) — never on every
  push to `main` or on pull requests, since publishing an image is deliberate, not a continuous side
  effect of every commit. Builds and pushes the `production` (app) and `web` (Nginx) images to GHCR,
  tagged together from the same commit so they can never mismatch. Needs no repository configuration
  beyond the automatically-provided `GITHUB_TOKEN`.

## Container Images

Every release produces two images from the same `Dockerfile`, tagged together from the same commit:

```text
ghcr.io/<owner>/<repo>        — production target: Laravel via PHP-FPM (also used by queue/scheduler)
ghcr.io/<owner>/<repo>-web    — web target: Nginx + that exact commit's compiled assets
```

Each publish tags exactly two things: `latest` and the pushed release tag (`v1.2.0`). There's no
commit-SHA tag or `main`-tracking tag — they'd add sprawl without a corresponding rollback benefit at
this project's scale, since every release is already uniquely identified by its `vX.Y.Z` tag. **Pin
`docker-compose.prod.yml`'s `APP_IMAGE_TAG` to a specific release once predictable upgrades matter more
than always running the newest build** — that pinned tag is what makes rollback (below) possible.

## Portainer Deployment

Deploy `docker-compose.prod.yml` as a Portainer Stack — the same pull-only, paste-and-deploy model
already used for this organization's baby-tracker project. It consumes prebuilt images only; nothing
is built on the server, and every value is inlined directly in the compose file via `${VAR:-default}`
substitution. There is no `.env` file to create on the host and no dependency on Portainer's own
.env-writing behavior — Portainer's **Environment variables** section (or plain shell env vars for a
CLI deploy) is the only source of truth.

1. In Portainer, go to **Stacks → Add stack**, choose **Web editor**, and paste in the contents of
   [`docker-compose.prod.yml`](docker-compose.prod.yml).
2. Under **Environment variables**, add the required values:

   | Variable | Notes |
   |---|---|
   | `APP_KEY` | Required — generate with `docker run --rm ghcr.io/daezerwin/fan-voted-music-chart php artisan key:generate --show`. |
   | `APP_URL` | e.g. `https://charts.example.com`. Defaults to `http://localhost:8080`. |
   | `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Required. Used by the `database` container and by `app`/`queue`/`scheduler` to connect to it. |
   | `REDIS_PASSWORD` | Required. Used by the `redis` container (`--requirepass`) and by `app`/`queue`/`scheduler` to connect to it. |

   Everything else (`APP_IMAGE_TAG`, `APP_PORT`, `APP_TIMEZONE`, `MAIL_*`, `FACEBOOK_*`,
   `YOUTUBE_API_KEY`, ...) has a sensible default and only needs setting when you want to override it.
   Pin `APP_IMAGE_TAG` to a specific release tag (e.g. `v0.2.0`) instead of tracking `latest` once you
   care about predictable, reproducible upgrades.
3. Click **Deploy the stack**.

If the GHCR images are private, add a registry credential in Portainer (or a `docker login` on the
host) before deploying — otherwise the stack will fail to pull.

## Production Deployment

```text
Push a vX.Y.Z tag → docker-publish.yml builds + pushes to GHCR (tags: vX.Y.Z, latest)
  → operator: Portainer "Pull and redeploy" → container starts
  → migrations run automatically (idempotent, in the app entrypoint) → verify /up
```

Deployment is a manual, operator-triggered action, not an automatic webhook — deliberately. Once a
release is published to GHCR, an operator opens the stack in Portainer and clicks **Pull and
redeploy** (or runs `docker compose pull && docker compose up -d` over SSH) after confirming the
release is good. There's no separate automated "deploy" job to fail; the deploy step is the human
decision to pull the new tag. A failed build must prevent a new image from ever reaching GHCR, but
that's as far as automation goes — this keeps production deploys reviewed rather than a side effect of
pushing a tag. Automating the last step with a Portainer webhook is an optional future upgrade (see
`agents.md` → "Portainer Deployment Integration"), not the starting requirement.

Only the `app` service runs migrations, via `docker/php/entrypoint.sh` gated on `RUN_MIGRATIONS=true`
(set only on `app` in `docker-compose.prod.yml`). `queue` and `scheduler` boot the exact same image
with that variable unset, so they never race `app` — or each other — to migrate concurrently.
`app`, `queue`, and `scheduler` always use the same `APP_IMAGE_TAG` — never update one without the
others, or workers will run different code than the web app.

After redeploying, verify `/up` and check `docker compose logs` / the Portainer container logs before
considering the release complete — a successful pull is not proof of a healthy application.

## Rollback

Every published image is identifiable by its release tag. To roll back, change
`docker-compose.prod.yml`'s `APP_IMAGE_TAG` (or the Portainer stack's pinned variable) back to the
previous `vX.Y.Z` release and redeploy — no rebuild required, since every past release tag remains
pullable from GHCR. Run `php artisan migrate:status` after rolling back if the previous deploy
included a migration; a schema rollback is a separate, deliberate decision the tooling here doesn't
automate.

## Production Hardening Review

A deliberate pass across the spec's hardening checklist. What changed, and why:

* **Caching** — `App\Actions\Charts\GetLatestDailyChart` (the "what's the current chart" lookup hit by
  the homepage, the chart page, and all three discovery sections) is now cached for an hour and
  explicitly invalidated inside `GenerateDailyChart` the moment a new chart is generated — never left
  to expire stale. Nothing else is cached yet; the rest of the app is cheap enough not to need it.
* **Concurrency correctness (a real bug, not just a checklist item)** — chart-generation locking used
  to live only in the `charts:generate-daily` Artisan command. The admin "regenerate" tool calls
  `GenerateDailyChart` directly, bypassing the command entirely, so it was completely unprotected
  against racing the scheduled run. Moved the lock into the action itself (`Cache::lock(...)->block(3, ...)`)
  so every call path — scheduled, manual command, or admin-triggered — serializes correctly instead of
  racing. See `App\Actions\Charts\GenerateDailyChart`.
* **SEO** — added Open Graph/Twitter Card meta tags (layout-level, with a per-page `:image` prop used by
  song/artist pages), `/sitemap.xml` (dynamic, cached, covers active artists/songs/genres), and pointed
  `robots.txt` at it.
* **Accessibility** — a global `:focus-visible` outline for every interactive element (previously only
  a few components had hand-added focus styles) and `prefers-reduced-motion` support.
* **Security** — added a `throttle:auth` rate limit (15/min/IP) to the Facebook redirect/callback routes
  as defense-in-depth. Reviewed for the usual suspects (mass assignment, XSS, SQL injection, open
  redirects, session fixation, sensitive logging) — nothing new found; see "Security" below for what's
  already in place from earlier phases.
* **Abuse prevention** — votes now record the casting IP (`votes.ip_address`, nullable, audit-only —
  it never blocks or throttles a vote by itself). The admin voting-activity page surfaces IPs used by
  more than one account on a given day, explicitly framed as a prompt for human review, not evidence of
  abuse (shared IPs are normal — households, NAT, campus networks).
* **Queue reliability** — already adequate: `failed_jobs`/`job_batches` tables exist, `queue:work` runs
  with `--tries=3`. Nothing in the app dispatches a queued job yet (votes are synchronous by design;
  YouTube metadata refresh and notifications are unbuilt), so there's nothing further to harden here
  until a real job exists.
* **Scheduler reliability** — already adequate: `withoutOverlapping()` at the schedule level plus the
  action-level lock above.

Deliberately **not** addressed here, because they need a real decision or real infrastructure this
environment doesn't have, not more code:

* **Backups** — a hosting/ops decision (which managed MySQL backup solution, retention policy), not
  something to fake-implement. Document the chosen approach once the production host is finalized.
* **Monitoring** — needs a real APM/error-tracking choice (Sentry, etc.) and credentials.
* **Privacy** — no account-deletion/data-export flow exists. Not building it speculatively; revisit if
  a real jurisdiction's requirements (GDPR/CCPA) apply once there are real users.
* **Full anti-fraud scoring** — the spec explicitly prefers "risk scoring and moderation over simplistic
  blocking" and warns never to reveal fraud thresholds publicly. Building a real one needs real vote
  patterns to calibrate against; today's daily-unique-vote constraint plus IP visibility (above) is the
  honest, non-theatrical state of anti-abuse for a pre-launch product.

## Troubleshooting

* **`docker compose exec app ...` fails with "no such service"** — run `docker compose up -d` first.
* **Permission errors under `storage/` or `bootstrap/cache/`** — these paths must be writable by the
  container's `laravel` user (uid 1000); avoid `chmod -R 777`.
* **Assets not updating** — rebuild with `docker compose exec app npm run build`, or run `npm run dev`
  for live reload.
* **`web` shows unhealthy in Portainer** — its healthcheck hits nginx's own `/healthz` (no PHP involved),
  so this means nginx itself isn't serving, not that `app` is degraded. Check the `web` container logs
  first; a failing/restarting `app` no longer flips `web` to unhealthy.
* **No shell in a container from Portainer (e.g. "`sh`: executable file not found")** — run the artisan
  command directly instead of opening a shell first: in Portainer's Console dialog (or
  `docker exec <container>`), put the actual command in the "Command" field, e.g.
  `php artisan migrate:status`, rather than `/bin/sh` followed by typing it interactively. This also
  sidesteps whatever is breaking the interactive shell.

## Security

See the Security section of [agents.md](agents.md). Report concerns to the maintainers rather than
filing a public issue if the concern is exploitable.

## License

Not yet decided.
