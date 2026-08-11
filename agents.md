# AGENTS.md

# Project: Fan-Voted Music Chart Platform

## Project Mission

Build a production-quality fan-voted music discovery and ranking platform using Laravel.

The product allows users to:

* Discover music.
* Browse artists and songs.
* Authenticate using social login.
* Vote for songs.
* View daily and historical rankings.
* Track chart movement.
* Play the current Top 10 using the official YouTube embedded player.
* Discover trending songs, new entries, and biggest movers.

The platform should feel like an independent community-driven music chart.

It must NOT present itself as Billboard or imply affiliation with Billboard.

YouTube is primarily the playback and metadata provider.

The primary value of the application is our own:

* Community voting.
* Ranking algorithm.
* Daily chart.
* Historical chart data.
* Artist and song discovery.
* Chart analytics.
* Editorial content.
* Community engagement.
* Music discovery experience.

---

# Your Role

Act as a senior Laravel architect, product engineer, DevOps engineer, and security-conscious maintainer.

You are working on a real production application.

Do not approach tasks as isolated demos.

Before changing anything:

1. Inspect the existing repository.
2. Understand the current architecture.
3. Read relevant documentation.
4. Inspect existing tests.
5. Inspect Docker configuration.
6. Inspect CI/CD configuration when relevant.
7. Preserve existing working behavior unless explicitly changing it.

Prefer conventional Laravel architecture.

Prefer simple, maintainable solutions.

Do not introduce complexity merely because a more elaborate architecture exists.

---

# Core Engineering Principles

Always prioritize:

1. Correctness.
2. Data integrity.
3. Voting integrity.
4. Security.
5. Maintainability.
6. Testability.
7. User experience.
8. Performance.
9. Accessibility.
10. SEO.
11. Deployment reliability.
12. Scalability when justified.

Use:

simple → tested → measurable → scalable

instead of:

complex → theoretical → difficult to maintain

Build the application so it can grow substantially without prematurely designing infrastructure for hundreds of millions of users.

---

# Technology Stack

Primary application stack:

* PHP
* Laravel
* Blade
* Tailwind CSS
* Alpine.js where lightweight client-side interaction is needed
* Laravel Socialite
* MySQL or PostgreSQL
* Redis
* Laravel queues
* Laravel Scheduler
* YouTube IFrame Player API
* YouTube Data API where necessary

Infrastructure:

* Docker
* Docker Compose
* Portainer
* GitHub
* GitHub Actions
* GitHub Container Registry (GHCR)

Prefer Laravel-native functionality before adding third-party packages.

Do not introduce:

* React
* Vue
* Inertia
* Livewire
* Kubernetes
* Elasticsearch
* RabbitMQ
* Kafka

unless the existing application already uses them or a genuine requirement justifies them.

Keep frontend JavaScript lightweight.

---

# Product Architecture

The application is a fan-voted music chart.

Users should eventually be able to:

* Browse today's Top 100.
* Browse today's Top 10.
* Browse historical charts.
* Play today's Top 10.
* Browse artists.
* Browse songs.
* Browse genres.
* Sign in with Facebook.
* Vote for songs.
* See ranking movement.
* See peak positions.
* See chart history.
* Discover new entries.
* Discover trending songs.
* Discover biggest gainers.
* Search artists and songs.
* Share chart, song, and artist pages.

Administrators should eventually be able to:

* Add artists.
* Edit artists.
* Add songs.
* Edit songs.
* Associate songs with YouTube videos.
* Manage genres.
* Enable or disable songs.
* Enable or disable voting.
* Moderate submissions.
* Review suspicious voting activity.
* Recalculate charts.
* Feature songs.
* Feature artists.
* Manage editorial content.
* Review basic platform analytics.

---

# Branding

The application must have its own identity.

Never claim or imply affiliation with Billboard.

Do not use:

* Billboard logos.
* Billboard trademarks as application branding.
* Visual designs intended to impersonate Billboard.
* Language implying our ranking is the Billboard Hot 100.

Acceptable language includes:

* Fan-Voted Top 100.
* Community Music Chart.
* Daily Top 100.
* Weekly Top 100.
* Fan Music Ranking.

The final product name must be configurable.

Prefer:

```env
APP_NAME="..."
```

Never unnecessarily hardcode the product name throughout templates.

---

# Core Domain Models

The conceptual domain should contain at least the following entities.

---

# User

Typical fields:

```text
id
name
email
avatar
email_verified_at
provider
provider_id
status
created_at
updated_at
```

Authentication provider information may be separated into another table if supporting multiple providers makes that design preferable.

Do not unnecessarily persist OAuth access tokens.

Never store OAuth secrets in the database as ordinary user fields.

---

# Artist

Typical fields:

```text
id
name
slug
bio
image
country
website
is_active
created_at
updated_at
```

Relationships:

```text
Artist hasMany Songs
```

---

# Song

Typical fields:

```text
id
artist_id
genre_id
title
slug
youtube_video_id
release_date
cover_image
description
is_active
voting_enabled
created_at
updated_at
```

Store the YouTube video ID.

Do not rely exclusively on complete YouTube URLs.

If administrators provide complete YouTube URLs, normalize them and extract the video ID.

---

# Genre

Typical fields:

```text
id
name
slug
created_at
updated_at
```

---

# Vote

Typical fields:

```text
id
user_id
song_id
vote_date
created_at
updated_at
```

Voting rules must be enforced server-side and at the database level.

Never rely only on disabled frontend controls.

---

# Chart

Represents an immutable or effectively immutable chart snapshot.

Typical fields:

```text
id
chart_type
chart_date
generated_at
created_at
updated_at
```

Example chart types:

```text
daily
weekly
```

---

# ChartEntry

Typical fields:

```text
id
chart_id
song_id
rank
vote_count
previous_rank
movement
peak_rank
charting_periods
created_at
updated_at
```

Historical chart snapshots must be stored.

Never reconstruct important historical charts exclusively from current mutable data.

---

# Voting Rules

Initial voting rule:

> A signed-in user may vote once for each eligible song per calendar day.

Unless explicitly changed, preserve this rule.

Use the application's configured business timezone when determining the voting day.

The timezone must be configurable.

Example:

```env
APP_TIMEZONE=Asia/Manila
```

Voting must include:

* Authentication.
* Validation.
* Eligibility checks.
* Server-side duplicate prevention.
* Database uniqueness protection.
* Rate limiting.
* Transaction safety.
* Appropriate error responses.

Create a database-level unique constraint equivalent to:

```text
user_id + song_id + vote_date
```

Design for concurrent requests.

Two simultaneous requests must not produce duplicate valid votes.

---

# Voting UX

Voting should feel immediate.

After a successful vote:

* Update the vote button.
* Clearly show success.
* Prevent accidental duplicate requests.
* Show that today's vote has been recorded.

The server remains authoritative.

Never fake a successful vote on the frontend before knowing persistence succeeded unless the UI can safely roll back failed optimistic state.

Useful states include:

```text
Vote
Voted Today
Sign In to Vote
Voting Closed
```

---

# Anti-Abuse

Chart credibility is one of the application's most important assets.

Whenever modifying voting, consider abuse.

Potential signals and protections include:

* Authenticated voting.
* Daily unique database constraints.
* Laravel rate limiting.
* IP-based signals.
* Session signals.
* Account age.
* Vote velocity.
* Unusual behavioral patterns.
* Duplicate-account indicators.
* Suspicious voting flags.
* Audit logs.
* Temporary voting restrictions.
* Administrator review.

Do not automatically ban users merely because multiple accounts use the same IP.

Shared IP addresses are common.

Prefer risk scoring and moderation over simplistic blocking.

Never reveal detailed fraud-detection thresholds publicly.

---

# Ranking Algorithm

For the initial MVP:

```text
Daily Score = Number of valid votes received during the chart date.
```

Primary sorting:

```text
vote_count DESC
```

Tie breaking must always be deterministic.

Recommended tie-break sequence:

1. Higher vote count.
2. Earlier time at which the final vote total was reached.
3. Better previous chart rank.
4. Stable song ID fallback.

Centralize ranking logic.

Potential class:

```text
CalculateChartRanking
```

Never scatter ranking calculations among:

* Controllers.
* Blade templates.
* JavaScript.
* Multiple unrelated query builders.

---

# Chart Generation

Implement chart generation as dedicated domain behavior.

A suitable Artisan command is:

```bash
php artisan charts:generate-daily
```

Chart generation should be:

* Deterministic.
* Testable.
* Logged.
* Idempotent where practical.
* Safe to retry.

Use transactions when producing snapshots.

Do not expose partially generated charts.

Use an application/distributed lock when duplicate scheduler execution could occur.

---

# Historical Charts

Store chart snapshots so historical data remains meaningful.

Song statistics should eventually support:

* Current rank.
* Previous rank.
* Movement.
* Peak rank.
* First charted date.
* Number of charting periods.
* Historical rankings.
* Highest daily vote count.

Avoid recalculating entire histories during every page request.

Use:

* Appropriate indexes.
* Cached aggregates.
* Precomputed statistics.

when warranted.

---

# Rank Movement

Support states including:

```text
▲ 3
▼ 2
—
NEW
RE-ENTRY
```

Do not communicate movement through color alone.

Use labels or symbols for accessibility.

---

# YouTube Integration

YouTube is used for playback and selected metadata.

Use only official methods including:

* YouTube embedded player.
* YouTube IFrame Player API.
* YouTube Data API where required.

Do NOT:

* Download YouTube videos.
* Download YouTube audio.
* Extract audio streams.
* Proxy media.
* Circumvent YouTube playback.
* Remove required YouTube branding.
* Cover official controls improperly.
* Interfere with YouTube advertising.
* Manufacture YouTube views.
* Manufacture likes.
* Automatically interact with YouTube engagement features.

Our votes are independent site votes.

They are not YouTube likes.

---

# Application Data vs YouTube Data

Keep first-party data logically distinct from YouTube-provided metadata.

First-party application data includes:

```text
Votes
Chart positions
Historical rankings
Genres
Editorial copy
Featured state
Moderation state
Internal artist metadata
Internal analytics
```

YouTube-derived data may include:

```text
Video ID
Video title
Channel
Thumbnail
Availability
Other API metadata
```

YouTube-derived metadata must be refreshable.

Do not assume externally supplied metadata is permanently valid.

---

# Top 10 Player

Provide a "Play Top 10" experience.

The queue must be generated from the current chart.

Use the official YouTube IFrame Player API.

Requirements:

* Player remains appropriately visible.
* Preserve official player behavior.
* Display currently playing song information.
* Gracefully handle unavailable videos.
* Gracefully handle disabled embedding.
* Skip unavailable entries when appropriate.
* Never create unauthorized audio-only playback.

Conceptually:

```text
Today's Chart
      |
      v
Current Top 10
      |
      v
Embeddable YouTube IDs
      |
      v
YouTube Queue
```

---

# Authentication

Use Laravel Socialite.

Initial provider:

```text
Facebook
```

Design authentication so future providers such as Google or Apple can be added without rewriting the user domain.

OAuth implementation must:

* Validate provider responses.
* Safely identify existing users.
* Create users correctly.
* Prevent accidental duplicate accounts where reasonable.
* Handle unavailable provider email addresses.
* Regenerate sessions after login.
* Preserve intended redirects where appropriate.

Do not blindly trust OAuth profile data.

---

# Authorization

Use Laravel:

* Middleware.
* Policies.
* Gates.
* Authorization checks.

Potential roles:

```text
user
moderator
admin
```

Avoid implementing an overly complicated role-permission system until needed.

Administrative security must always be server-side.

Hiding admin navigation is never sufficient authorization.

---

# Admin Architecture

Use clearly separated admin routes.

Example:

```text
/admin/*
```

Prefer:

* Dedicated controllers.
* Form Requests.
* Policies.
* Middleware.
* Blade components.

Do not allow sensitive model properties through unrestricted mass assignment.

Meaningful administrative changes should eventually be auditable.

---

# Controllers

Keep controllers thin.

Controllers should primarily:

1. Receive requests.
2. Authorize.
3. Validate through Form Requests.
4. Invoke domain actions/services.
5. Return responses.

Do not place complex:

* Ranking algorithms.
* OAuth account mapping.
* Fraud analysis.
* Chart generation.

inside controllers.

---

# Actions and Services

Use domain actions/services when behavior warrants them.

Examples:

```text
CastVote
GenerateDailyChart
CalculateChartRanking
RefreshYouTubeMetadata
BuildTopTenPlaylist
DetectSuspiciousVoting
```

Avoid unnecessary abstractions around simple Eloquent CRUD.

Architecture exists to reduce complexity, not produce ceremony.

---

# Validation

Use Laravel Form Requests for meaningful submissions.

Validate:

* YouTube URLs.
* YouTube video IDs.
* Artist IDs.
* Genre IDs.
* Voting eligibility.
* Song status.
* Dates.
* Slugs.
* Administrative input.
* User-editable fields.

Client-side validation is supplemental.

The server is authoritative.

---

# Database Standards

Every schema change must use migrations.

Consider:

* Foreign keys.
* Indexes.
* Unique constraints.
* Nullable fields.
* Delete behavior.
* Cascading behavior.
* Historical integrity.
* Large-table performance.

Important likely indexes include:

```text
votes.user_id
votes.song_id
votes.vote_date
votes(user_id, song_id, vote_date)

charts.chart_date
charts.chart_type

chart_entries.chart_id
chart_entries.song_id

songs.slug
songs.artist_id
songs.genre_id

artists.slug
genres.slug
```

Avoid N+1 queries.

Use eager loading deliberately.

---

# Data Deletion

Historical charts are valuable data.

Do not casually cascade-delete chart history when an artist or song disappears.

Consider:

* `is_active`.
* Archival.
* Soft deletes.

Historical chart entries should remain meaningful when a YouTube video becomes unavailable later.

---

# Tailwind Design System

Use Tailwind CSS.

The public product should feel:

* Modern.
* Music-focused.
* Premium.
* Energetic.
* Clean.
* Responsive.

Do not make the consumer-facing website resemble a generic admin dashboard.

Use reusable Blade components.

Potential components:

```text
ChartRow
RankBadge
MovementIndicator
SongCard
ArtistCard
GenreBadge
VoteButton
YouTubePlayer
ChartStats
Navigation
Pagination
Alert
Modal
EmptyState
LoadingState
```

---

# Responsive Design

Mobile is a first-class experience.

Every important public page must work well on:

* Mobile.
* Tablet.
* Desktop.

Avoid wide desktop-only tables that become unusable on mobile.

Prefer adaptive chart rows/cards.

---

# Accessibility

Follow reasonable WCAG-oriented practices.

Include:

* Semantic HTML.
* Proper headings.
* Form labels.
* Keyboard navigation.
* Visible focus indicators.
* Accessible buttons.
* Useful alt text.
* Sufficient contrast.
* Reduced-motion consideration.

Do not communicate information solely through color.

---

# Homepage

The homepage should emphasize discovery.

Recommended structure:

1. Current chart hero.
2. Current Top 10.
3. Play Top 10.
4. Biggest gainers.
5. New entries.
6. Trending songs.
7. Featured artists.
8. Genres.
9. Voting call to action.

Do not overload the first viewport.

---

# Chart Pages

A chart row should communicate:

```text
Current rank
Movement
Artwork
Song
Artist
Vote count where appropriate
Vote action
Play action
```

Example:

```text
#1    ▲ 2
Artist Name
Song Name
12,481 votes
[Vote] [Play]
```

Build reusable components instead of duplicating markup.

---

# Song Page

Eventually support:

* Song title.
* Artist.
* Artwork.
* Current rank.
* Previous rank.
* Movement.
* Peak rank.
* Vote count.
* Vote action.
* YouTube player.
* Release information.
* Genre.
* Chart history.
* Related songs.
* Sharing metadata.

A song page must provide meaningful independent content beyond the YouTube embed.

---

# Artist Page

Eventually support:

* Artist name.
* Image.
* Biography.
* Origin/country where appropriate.
* Current songs.
* Charting songs.
* Highest chart position.
* Song catalog.
* Relevant external links.
* Historical chart performance.

Do not copy third-party biographies without appropriate rights.

---

# Genre Pages

Genre pages may contain:

* Genre Top 50.
* Trending songs.
* New entries.
* Featured artists.

Genre definitions are owned by our application and should not depend entirely on YouTube metadata.

---

# SEO

Public content should be server-rendered and search-friendly.

Consider:

* Page title.
* Meta description.
* Canonical URL.
* Open Graph metadata.
* Social metadata.
* Structured data where appropriate.
* Stable slugs.
* Semantic headings.
* XML sitemap.
* Robots rules.

Avoid thin pages containing little more than an embedded YouTube video.

---

# URL Conventions

Prefer URLs similar to:

```text
/
/charts
/charts/daily
/charts/2026-08-10
/charts/weekly
/artists/{slug}
/songs/{slug}
/genres/{slug}
/login
/auth/facebook/redirect
/auth/facebook/callback
/admin/*
```

Use named Laravel routes.

Avoid exposing raw database IDs unnecessarily in public URLs.

---

# Caching

Cache data where useful.

Likely candidates:

* Current chart.
* Top 10.
* Homepage chart sections.
* Artist aggregates.
* Genre rankings.
* Popular/trending results.

Voting persistence must never depend on stale cache state.

Invalidate caches deliberately after chart generation.

Do not cache everything prematurely.

---

# Redis

Use Redis where useful for:

* Cache.
* Queue transport.
* Rate limiting.
* Distributed locks.

Do not treat Redis as the permanent authoritative source for votes or historical chart data.

---

# Queues

Use queues for work that should not block HTTP requests.

Candidates include:

* Refreshing YouTube metadata.
* Image processing.
* Notifications.
* Fraud-analysis jobs.
* Analytics aggregation.
* Exports.

Vote persistence itself should remain synchronous and reliable.

Do not queue the only copy of a vote that has not yet been persisted.

---

# Laravel Scheduler

The scheduler may handle:

```text
Daily chart generation
Weekly chart generation
YouTube metadata refresh
Unavailable video checks
Analytics aggregation
Cleanup jobs
```

Scheduled jobs must be safe to rerun.

---

# Monetization

The product may eventually be monetized through:

* Display advertising.
* Sponsorships.
* Clearly disclosed promoted placements.
* Sponsored editorial content.
* Artist promotional tools.
* Premium artist analytics.
* Affiliate links.
* Newsletter sponsorship.
* Brand partnerships.

Paid content must not secretly affect organic chart ranking.

Clearly distinguish:

```text
Organic ranking
```

from:

```text
Sponsored/promoted content
```

Do not put custom advertising inside or over the YouTube player.

---

# Security

Follow Laravel security best practices.

Always consider:

* CSRF.
* XSS.
* SQL injection.
* Authorization bypass.
* Mass assignment.
* OAuth account takeover.
* Session fixation.
* Open redirects.
* Vote replay.
* Rate-limit bypass.
* Unsafe file uploads.
* Sensitive logging.
* Secret leakage.

Never commit:

```text
API keys
OAuth secrets
Database passwords
Production access tokens
Private keys
Certificates
Webhook secrets
```

Use environment configuration.

---

# Error Handling

Do not display internal stack traces in production.

Give users useful messages.

Examples:

```text
You have already voted for this song today.
Voting is currently closed.
This video is unavailable.
Please sign in to vote.
This song is inactive.
```

Log appropriate technical detail server-side.

---

# Logging

Log meaningful events such as:

* Chart generation.
* Failed chart generation.
* OAuth failures.
* Suspicious voting spikes.
* Administrative moderation.
* YouTube metadata failures.
* Deployment-relevant application startup problems.

Never log:

* Passwords.
* OAuth secrets.
* Sensitive tokens.
* Unnecessary personal data.

---

# Testing Philosophy

Important behavior must be tested.

Prefer Laravel feature tests for product behavior.

---

# Authentication Tests

Include tests for:

* OAuth user creation.
* Existing-user login.
* Authentication rejection.
* Account mapping.
* Session behavior.

Mock external OAuth APIs where appropriate.

---

# Voting Tests

Include tests for:

* Guest cannot vote.
* Authenticated user can vote.
* User cannot vote for same song twice on same day.
* User may vote again on a later valid day.
* Inactive song cannot receive votes.
* Voting-disabled song cannot receive votes.
* Rate limiting where appropriate.
* Concurrent duplicate protection.

---

# Chart Tests

Include tests for:

* Correct sorting.
* Deterministic ties.
* Snapshot persistence.
* Previous ranking.
* Movement calculation.
* New-entry detection.
* Peak ranking.
* Safe reruns.
* Date/timezone boundaries.

---

# Authorization Tests

Verify:

* Ordinary users cannot access admin functionality.
* Moderators receive only permitted capabilities.
* Admins receive appropriate access.

---

# Page Tests

At minimum, test important routes such as:

```text
Homepage
Charts
Artist page
Song page
Genre page
Authentication
```

---

# Factories and Seeders

Provide useful factories and development seeders.

Seed data should include:

* Users.
* Artists.
* Genres.
* Songs.
* Votes.
* Charts.
* Chart entries.

Development data should exercise UI states including:

```text
NEW
▲ movement
▼ movement
unchanged
re-entry
ties
inactive songs
unavailable videos
```

Never automatically run destructive demo seeders in production.

---

# PHP Coding Style

Follow repository conventions first.

Otherwise use:

* PSR conventions.
* Laravel conventions.
* Typed signatures where practical.
* Clear names.
* Small methods.
* Early returns.
* Minimal nesting.

Use PHP enums where they improve correctness.

Good candidates:

```text
ChartType
AccountStatus
ModerationStatus
```

Do not turn database-managed categories such as genres into hardcoded enums.

---

# Comments

Comments should explain why.

Bad:

```php
// Get votes
$votes = Vote::query()->get();
```

Good:

```php
// Historical chart snapshots intentionally use stored totals so
// later moderation does not silently rewrite published rankings.
```

Avoid excessive comments.

---

# Docker and Portainer Environment

The development and deployment environment uses Docker and Portainer.

Do not assume PHP, Composer, Node, MySQL, PostgreSQL, Redis, or other dependencies are installed directly on the host.

The repository must contain enough configuration to bootstrap the application through Docker.

Expected files may include:

```text
Dockerfile
.dockerignore
docker-compose.yml
docker-compose.dev.yml
docker/
```

Only create files actually required.

---

# Docker Compose Requirement

The project must be runnable through Docker Compose.

A normal startup should be equivalent to:

```bash
docker compose up -d
```

Where initialization commands are still required, document them clearly in `README.md`.

The Compose environment should eventually support:

```text
web
app
database
redis
queue
scheduler
```

Frontend build tooling may use a dedicated development service if helpful.

---

# Recommended Docker Architecture

Conceptually:

```text
                 Internet
                    |
                    v
               +---------+
               |  Nginx  |
               +----+----+
                    |
                    v
              +-----------+
              | Laravel   |
              | PHP-FPM   |
              +-----+-----+
                    |
          +---------+----------+
          |         |          |
          v         v          v
      Database    Redis      Queue
                              Worker

                    |
                    v
                Scheduler
```

Do not add infrastructure without justification.

---

# Docker: Application Service

The application service should run Laravel through PHP-FPM.

Responsibilities:

* PHP runtime.
* Laravel application.
* Composer dependencies.

Do not run unrelated infrastructure in the application process.

---

# Docker: Web Service

Use Nginx or another deliberate web server.

Nginx configuration should remain under source control.

For example:

```text
docker/nginx/default.conf
```

Nginx should:

* Serve public/static files.
* Pass PHP requests to PHP-FPM.
* Apply reasonable production defaults.

---

# Docker: Database

Use the database engine chosen by the application.

Preferred options:

* PostgreSQL.
* MySQL.

Use persistent named volumes.

Production database data must never depend on an ephemeral container filesystem.

Do not expose the production database port publicly unless explicitly required.

---

# Docker: Redis

Run Redis as an internal service.

Possible purposes:

* Cache.
* Queue.
* Rate limiting.
* Locks.

Do not expose Redis publicly in production.

---

# Docker: Queue

Queue workers should be separate from HTTP workers.

A typical command:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

Exact production parameters should reflect application workload.

Configure restart behavior.

When the application image changes, queue services must also update.

---

# Docker: Scheduler

Run the Laravel scheduler as its own service.

A suitable approach:

```bash
php artisan schedule:work
```

Prefer this over relying on host-level cron because the application is managed through Portainer.

---

# Docker Networking

Use internal Docker networks.

Normally expose externally only the service that needs inbound HTTP traffic.

Keep internal:

```text
app
database
redis
queue
scheduler
```

Development overrides may expose database ports if explicitly useful.

---

# Persistent Storage

Use named volumes or deliberate persistent storage.

Examples:

```text
database_data
redis_data
```

Persistent user uploads should eventually use:

* Docker volume.
* Object storage.
* Another deliberate storage provider.

Do not assume container-local files survive container replacement.

---

# Laravel Storage Permissions

Ensure these paths are writable:

```text
storage/
bootstrap/cache/
```

Do not solve permissions with:

```bash
chmod -R 777
```

Use appropriate ownership and permissions.

---

# Dockerfile Standards

Prefer multi-stage builds.

Conceptual stages:

```text
Composer dependencies
        |
        v
Frontend/Vite build
        |
        v
Production PHP image
```

Production images should not contain unnecessary:

* Compilers.
* Node development dependencies.
* Composer cache.
* npm cache.
* Test artifacts.
* Git history.
* Development tools.

---

# Composer Builds

Production builds should use the committed lock file.

Equivalent:

```bash
composer install \
  --no-dev \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader
```

Never automatically run:

```bash
composer update
```

during production deployment.

---

# Frontend Builds

Use the committed npm lock file.

Prefer:

```bash
npm ci
npm run build
```

over:

```bash
npm install
```

for reproducible CI and production builds.

Do not require Node.js inside the final PHP runtime image if only compiled static assets are needed.

---

# Docker Health Checks

Provide meaningful health checks where practical.

Laravel may expose a health route such as:

```text
/up
```

Do not expose sensitive infrastructure details through health responses.

Health checks should help determine whether the application is ready to receive traffic.

---

# Startup Behavior

Do not use arbitrary `sleep 30` style commands as the primary dependency solution.

Prefer:

* Container health checks.
* Retry-safe commands.
* Explicit deployment sequencing.
* Database readiness handling.

Container startup should be deterministic.

---

# Database Migrations and Docker

Do not execute production migrations during Docker image build.

Image build and schema migration are separate concerns.

Mirroring this organization's baby-tracker deployment, run migrations automatically from the `app` container's entrypoint script on every startup (`php artisan migrate --force`) instead of treating it as a separate manual deployment step. Laravel migrations are designed to be idempotent/rerunnable, so this is safe on every deploy and removes an operator step from the release process.

Only the `app` service's entrypoint should run migrations. The `queue` and `scheduler` services boot the exact same image but must skip the migration step (e.g. via an entrypoint argument or environment flag), so multiple containers never race to migrate concurrently.

---

# Environment Variables

Maintain a useful `.env.example`.

Potential values include:

```env
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=
APP_TIMEZONE=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=

CACHE_STORE=
SESSION_DRIVER=
QUEUE_CONNECTION=

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=

YOUTUBE_API_KEY=
```

Add new required environment variables to `.env.example`.

Never put real secrets in `.env.example`.

---

# Portainer

Portainer is the expected development/deployment container management environment.

Docker Compose files must remain suitable for Portainer Stacks.

Avoid unnecessary Docker functionality that prevents normal Portainer deployment.

Preferred deployment model — the same one already used for this organization's baby-tracker deployment:

```text
GitHub
  |
  v
Git tag push (vX.Y.Z)
  |
  v
GitHub Actions (build + push)
  |
  v
GitHub Container Registry
  |
  v
Portainer Stack (pull-only, Web editor)
  |
  v
Application
```

Production should run prebuilt immutable images pulled directly by Portainer — never rebuilt on the Portainer host for normal deployments.

The default deployment path is an operator-triggered "Pull and redeploy" in Portainer after a new tag is published, not an automatic webhook. Webhook-triggered automatic redeploy is an optional future enhancement (see "Portainer Deployment Integration"), not the starting requirement.

---

# GitHub Container Registry

Use GHCR by default unless another registry is explicitly selected.

Images should follow a convention similar to:

```text
ghcr.io/<owner>/<repository>:latest
ghcr.io/<owner>/<repository>:v1.2.0
```

Publish a new image only when a version tag (`vX.Y.Z`) is pushed, or via manual `workflow_dispatch` — not on every push to `main`. Tag the published image with the exact Git tag plus `latest`. That's sufficient traceability at this project's scale; a mandatory commit-SHA tag, a `main`-tracking tag, or auto-derived semver major/minor tags add tag sprawl without a corresponding rollback or traceability benefit.

Pin `docker-compose.prod.yml` to a specific `vX.Y.Z` tag instead of `latest` when predictable upgrades matter more than always running the newest build.

Never rely exclusively on `latest` when a specific rollback target matters.

---

# Production Docker Compose

Production Compose should consume prebuilt images.

Prefer:

```yaml
image: ghcr.io/example/music-chart:${APP_IMAGE_TAG:-latest}
```

instead of rebuilding the application on the production server.

Development Compose may use:

```yaml
build:
  context: .
```

---

# Application Image Consistency

All services running Laravel code must use the same image.

For example:

```yaml
services:

  app:
    image: ghcr.io/example/music-chart:${APP_IMAGE_TAG}

  queue:
    image: ghcr.io/example/music-chart:${APP_IMAGE_TAG}

  scheduler:
    image: ghcr.io/example/music-chart:${APP_IMAGE_TAG}
```

Never accidentally deploy different source versions between:

```text
web application
queue
scheduler
```

---

# Portainer Stack Variables

Compose intended for Portainer should support environment substitution.

Possible variables:

```text
APP_IMAGE_TAG
APP_URL
APP_ENV

DB_DATABASE
DB_USERNAME
DB_PASSWORD

REDIS_PASSWORD
```

Never hardcode production-specific credentials or hostnames unnecessarily.

---

# GitHub Actions

Use GitHub Actions to build and publish the Docker image.

The publish workflow triggers on:

* A pushed version tag (`v*.*.*`).
* Manual `workflow_dispatch`.

It does not need to trigger on every push to `main` or on pull requests — publishing an image is a deliberate release action, not a continuous side effect of every commit, mirroring how this organization's baby-tracker project publishes.

A separate, optional `ci.yml` may still run formatting/static analysis/tests/frontend build on pull requests and pushes to `main` as a development safety net. Keep it if the team wants that safety net, but it does not gate or trigger the publish workflow — the two are independent. Pushing a release tag is what publishes an image, regardless of `ci.yml`'s state.

Never manually push directly-built images to GHCR from a developer machine — publishing always goes through the workflow so every published image is reproducible from source.

---

# GitHub Workflow Structure

Prefer a single workflow for the common case:

```text
.github/
└── workflows/
    └── docker-publish.yml
```

`docker-publish.yml` triggers on pushed version tags and `workflow_dispatch`, builds the image(s), and pushes to GHCR — no separate `ci.yml`/`deploy.yml` handoff is required for the image to reach the registry.

Split into multiple workflows only when a genuine reason exists (e.g. a team that wants PR-time validation kept separate from release publishing). Keep any additional workflow (such as `ci.yml`) independent of and non-blocking for `docker-publish.yml`.

---

# CI: Composer

Use the lock file.

Equivalent:

```bash
composer install --prefer-dist --no-interaction
```

Use dependency caching appropriately.

Do not mutate `composer.lock` during CI.

---

# CI: Laravel Pint

If Pint is configured:

```bash
./vendor/bin/pint --test
```

CI should report formatting violations rather than silently modifying code.

---

# CI: Static Analysis

If Larastan/PHPStan is configured:

```bash
./vendor/bin/phpstan analyse
```

Do not add excessive static-analysis infrastructure simply because this file mentions it.

Introduce it deliberately when suitable.

---

# CI: Tests

Run:

```bash
php artisan test
```

or the repository's established equivalent.

CI tests must use isolated test infrastructure.

Never point CI at a production database.

---

# CI: Database

Important integration tests should preferably run against the same database engine as production.

Use GitHub Actions service containers when appropriate.

SQLite is acceptable for compatible tests, but do not let production database-specific behavior remain completely untested.

---

# CI: Frontend

Run:

```bash
npm ci
npm run build
```

CI should fail if production assets cannot compile.

---

# CI: Docker

Validate Docker configuration.

Useful commands include:

```bash
docker compose config
```

and:

```bash
docker build .
```

Do not claim Docker configuration works unless validated when the execution environment allows it.

---

# Docker Image Workflow

Use Docker BuildKit/buildx where appropriate.

Build the production image only after or alongside required verification.

Use build caching where it significantly improves workflow speed.

Publish only successful builds.

---

# Docker Tags

Publish two tags per release:

```text
latest
v1.0.0   (the exact pushed Git tag)
```

Do not add a mandatory commit-SHA tag, a `main`-tracking tag, or auto-derived semver major/minor tags unless a real need for them shows up — they add tag sprawl without a corresponding rollback or traceability benefit at this project's scale. `vX.Y.Z` is already unique and traceable to a commit via the annotated tag.

---

# GitHub Permissions

Follow least privilege.

A container publishing job may require:

```yaml
permissions:
  contents: read
  packages: write
```

Do not grant:

```text
contents: write
actions: write
admin-style permissions
```

unless genuinely needed.

---

# GitHub Secrets

The default deployment path (operator-triggered "Pull and redeploy" in Portainer) needs no deployment secrets in GitHub at all — the built-in `GITHUB_TOKEN` is sufficient to publish to GHCR.

Only add secrets such as:

```text
PORTAINER_WEBHOOK
PORTAINER_URL
PORTAINER_API_TOKEN
```

if/when automatic webhook-triggered redeploy is deliberately introduced later. Never commit these values; never print secrets in workflow logs.

Application runtime secrets should normally stay in Portainer/runtime configuration rather than being injected into Docker image builds.

---

# GitHub Environments

GitHub Environments (`staging`, `production`) are not required for the default deployment path — there is no automated deployment step to protect.

Introduce them only if/when automatic Portainer webhook deployment is added later, so production secrets stay gated behind environment protection rules. Pull requests must never receive production secrets either way.

---

# Production Deployment

Preferred flow — the same one used for this organization's baby-tracker deployment:

```text
Push a vX.Y.Z tag
      |
      v
GitHub Actions builds the image
      |
      v
Push to GHCR (tags: vX.Y.Z, latest)
      |
      v
Operator: Portainer "Pull and redeploy"
      |
      v
Container starts
      |
      v
Migrations run automatically (idempotent, in the entrypoint)
      |
      v
Verify health (/up)
```

A failed build must prevent a new image from ever reaching GHCR — but there is no separate automated "deploy" job to fail. The deploy step is the operator pulling the new tag in Portainer once they've confirmed the release is good.

---

# Portainer Deployment Integration

Preferred approach — pull-only stack, no webhook:

## Pull-only Stack (default)

Maintain a `docker-compose.prod.yml` that references only the prebuilt GHCR image (no `build:` context). An operator pastes it into Portainer's Web editor once, sets the required environment variables, and deploys. To update to a new release, the operator opens the stack in Portainer and clicks **Pull and redeploy** (or runs `docker compose pull && docker compose up -d` over SSH).

This requires no GitHub secrets, no webhook, and no Portainer API access.

## Portainer Stack Webhook (optional, later)

Once the manual flow is trusted, GitHub Actions may optionally trigger an existing Portainer Stack webhook after a valid image has been pushed. Store the webhook as a GitHub Secret; never commit the webhook URL. Treat this as a deliberate future upgrade, not a starting requirement.

## Portainer API (last resort)

Use the API only when neither of the above can provide required deployment behavior. Use narrow-scoped credentials; never hardcode Portainer API credentials.

---

# Manual Deployment

The standard, steady-state deployment model for this project is:

1. The release workflow builds the image on a version-tag push.
2. The workflow pushes the image to GHCR.
3. An operator updates the Portainer stack to the new immutable image tag ("Pull and redeploy").

This is not a temporary stopgap on the way to full automation — it's a deliberate choice that keeps production deploys a reviewed, operator-initiated action rather than a side effect of pushing a tag. Automating the last step with a webhook is optional and should only be added if the team decides the manual click is genuinely a bottleneck.

---

# Deployment Rollback

Every published image is identifiable by its release tag.

Example:

```text
Current:
v1.0.16

Previous:
v1.0.15
```

Rollback means changing the `image:` tag in `docker-compose.prod.yml` (or the Portainer stack's pinned tag) back to the previous `vX.Y.Z` release and redeploying. Do not require rebuilding historical source code to roll back — every past release tag remains pullable from GHCR.

---

# Queue Deployment

Laravel queue workers keep application code in memory.

After deployments, ensure workers use the new image.

In Docker, replacing/recreating the queue container is preferred.

Do not leave old queue workers running old application code.

---

# Scheduler Deployment

The scheduler must use the exact same application image version as the primary application.

---

# Health Verification

A successful "Pull and redeploy" is not proof of a healthy application.

After redeploying, the operator should verify the health endpoint (e.g. `/up`) and check `docker compose logs` / the Portainer container logs before considering the release complete.

If a future webhook-based automated deploy is introduced, that workflow must verify health itself and clearly fail rather than silently reporting success.

---

# Branch Strategy

Keep Git workflow simple initially.

Recommended:

```text
feature/*
    |
    v
Pull Request
    |
    v
CI
    |
    v
main
    |
    v
Build
    |
    v
Deploy
```

Do not introduce Git Flow or multiple long-lived branches without a real requirement.

---

# Branch Protection

Recommend protecting `main`.

Important checks may include:

```text
PHP tests
Pint
Static analysis
Frontend build
Docker build
```

Use required PR reviews if appropriate for the team.

---

# Dependabot

Consider:

```text
.github/dependabot.yml
```

for:

* Composer.
* npm.
* GitHub Actions.

Do not automatically merge risky major upgrades.

Dependency changes must pass normal CI.

---

# GitHub Actions Security

Never expose production credentials to code from untrusted pull requests.

Review third-party GitHub Actions before adding them.

Use official or well-maintained actions.

Pin actions to appropriate stable versions or commit SHAs according to repository security policy.

Use the narrowest permissions possible.

---

# Docker Security

Production containers should follow good container security practices.

Prefer:

* Minimal base images.
* Supported runtime versions.
* Non-root processes where practical.
* Limited packages.
* No SSH daemon.
* No Docker socket.
* No privileged mode.
* No embedded secrets.

Avoid root runtime where reasonably possible.

---

# .dockerignore

Maintain `.dockerignore`.

Likely exclusions:

```text
.git
.env
.env.*
node_modules
vendor
storage/logs/*
IDE files
local caches
test output
```

Do not accidentally exclude files needed by the build.

---

# README.md Is Mandatory

The repository must contain a useful, accurate, maintained:

```text
README.md
```

README maintenance is part of the definition of done.

Do not treat README documentation as optional cleanup.

Whenever a change affects:

* Installation.
* Development workflow.
* Docker.
* Portainer.
* Environment variables.
* Database setup.
* Redis.
* Authentication.
* YouTube integration.
* Queue workers.
* Scheduler.
* Testing.
* CI/CD.
* Deployment.
* Common commands.
* Architecture.

update `README.md` in the same task.

Do not leave README instructions knowingly stale.

---

# README Goal

A developer unfamiliar with the project should be able to understand:

1. What the application does.
2. What technologies it uses.
3. How to start development.
4. How Docker is structured.
5. How to configure the environment.
6. How to create the database.
7. How to run migrations.
8. How to seed development data.
9. How to run tests.
10. How to run frontend builds.
11. How social authentication is configured.
12. How YouTube integration is configured.
13. How queues run.
14. How scheduled jobs run.
15. How GitHub Actions work.
16. How images reach GHCR.
17. How Portainer deployment works.
18. How deployment rollback works.

The README should be practical rather than promotional.

---

# Required README Structure

Unless the repository has a better established structure, maintain sections equivalent to:

```text
# Project Name

## About

## Features

## Technology Stack

## Architecture

## Requirements

## Local Development

## Docker Development

## Environment Variables

## Database Setup

## Development Seed Data

## Authentication

## YouTube Integration

## Voting System

## Chart Generation

## Queue Workers

## Scheduler

## Testing

## Code Quality

## Common Commands

## Docker Services

## GitHub Actions

## Container Images

## Portainer Deployment

## Production Deployment

## Rollback

## Troubleshooting

## Security

## License
```

Only include sections that meaningfully apply.

---

# README: Quick Start

The README must contain an accurate quick-start flow.

It should be as simple as reasonably possible.

An example structure:

```bash
git clone <repository-url>
cd <repository>

cp .env.example .env

docker compose up -d

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

If Composer dependencies are installed during image build, do not document unnecessary Composer installation commands.

If Node development runs in Docker, document that.

If Node runs on the host, document the requirement.

The README must describe the actual implementation, not an imaginary generic setup.

---

# README: Portainer Instructions

Document how to deploy the application as a Portainer Stack.

Include relevant information such as:

* Required image.
* Required stack variables.
* Required secrets.
* Required volumes.
* Required networks.
* Initial migration procedure.
* Deployment update procedure.
* Rollback procedure.

Never include actual production secrets.

Use placeholders.

---

# README: Environment Variables

Document each meaningful custom environment variable.

For example:

```text
APP_TIMEZONE
FACEBOOK_CLIENT_ID
FACEBOOK_CLIENT_SECRET
FACEBOOK_REDIRECT_URI
YOUTUBE_API_KEY
APP_IMAGE_TAG
```

Explain what the value represents.

Do not repeat Laravel's entire default `.env` documentation unnecessarily.

---

# README: GitHub Actions

Document the workflows.

Explain:

```text
docker-publish.yml
ci.yml   (optional, if present)
```

Document:

* Their triggers (`docker-publish.yml` fires on a pushed `vX.Y.Z` tag or manual dispatch — not on every push to `main`).
* What each validates.
* When images are produced.
* Which tags are published (`latest` and the exact release tag).
* That deployment is a manual, operator-triggered "Pull and redeploy" in Portainer, not automatic.

---

# README: Common Commands

Include a practical command reference.

Example:

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

# Seed
docker compose exec app php artisan db:seed

# Tests
docker compose exec app php artisan test

# Tinker
docker compose exec app php artisan tinker

# Queue
docker compose logs -f queue

# Scheduler
docker compose logs -f scheduler
```

Only document commands that match the real Compose service names.

---

# README: Architecture Diagram

When useful, include a simple Markdown-compatible ASCII architecture diagram.

Example:

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

Keep diagrams maintainable.

Do not require special proprietary rendering tools to understand the architecture.

---

# README: Development vs Production

Clearly distinguish development and production behavior.

Examples:

```text
Development:
- Local image builds.
- Debug enabled.
- Vite development server where applicable.

Production:
- GHCR image.
- Debug disabled.
- Compiled frontend.
- Portainer Stack.
- Persistent database volumes.
```

Never recommend production settings such as:

```env
APP_DEBUG=true
```

---

# README Accuracy Rule

Never document a command without checking whether it matches the repository.

Never describe a service that does not exist.

Never document a path that has not been created.

Whenever infrastructure changes, audit the related README section.

Incorrect documentation is considered a bug.

---

# Optional Makefile

A small Makefile may be introduced for developer convenience.

Potential commands:

```text
make up
make down
make test
make shell
make migrate
make seed
make logs
make build
```

These should remain thin wrappers around actual Docker commands.

The project must remain operable without understanding complicated custom scripts.

If a Makefile is added, document it in `README.md`.

---

# Documentation Beyond README

Use dedicated documentation files when a subject becomes too detailed for the README.

Potential structure:

```text
docs/
├── architecture.md
├── deployment.md
├── voting.md
└── chart-ranking.md
```

Do not fragment documentation unnecessarily.

README should remain the starting point.

When detailed documentation exists, link to it from README.

---

# Developer Experience

A new developer should be able to clone the repository and get a development environment running with minimal host dependencies.

Prefer requiring only:

```text
Git
Docker
Docker Compose
```

where practical.

Avoid requiring developers to manually install matching versions of PHP, Composer, Node, MySQL, and Redis when Docker already provides them.

---

# Production Hardening

Before considering production deployment complete, review:

* `APP_ENV=production`.
* `APP_DEBUG=false`.
* HTTPS.
* Secure cookies.
* Trusted proxies if required.
* Correct queue configuration.
* Correct cache configuration.
* Database backups.
* Persistent storage.
* Log handling.
* Health checks.
* Scheduler.
* Queue worker restart behavior.
* Rate limiting.
* OAuth callback URLs.
* YouTube API credentials.
* Security headers.
* Deployment rollback.
* Monitoring.

Do not claim production readiness merely because the containers start.

---

# Backups

Database persistence is not the same as backup.

Production planning should include database backups.

Never delete or reset production data as part of routine deployment.

Document the backup responsibility once the deployment environment is finalized.

---

# Development Phases

Unless explicitly instructed otherwise, develop incrementally.

---

## Phase 1 — Project Foundation

Implement:

* Laravel project.
* Docker environment.
* Docker Compose.
* Nginx.
* PHP runtime.
* Database.
* Redis.
* Tailwind.
* Base layout.
* Environment configuration.
* README.
* Initial CI.

Acceptance criteria:

* `docker compose up -d` succeeds.
* Laravel responds.
* Database connection succeeds.
* Redis connection succeeds.
* Tests run.
* Frontend builds.
* README setup instructions work.

---

## Phase 2 — Domain Foundation

Implement:

* Users.
* Artists.
* Genres.
* Songs.
* Migrations.
* Models.
* Relationships.
* Factories.
* Seeders.
* Admin foundation.

---

## Phase 3 — Public Catalog

Implement:

* Homepage.
* Artist pages.
* Song pages.
* Genre pages.
* Responsive components.
* Search foundation.

---

## Phase 4 — Authentication

Implement:

* Laravel Socialite.
* Facebook OAuth.
* User creation.
* Existing-user authentication.
* Navigation authentication state.
* Authentication tests.

Update README with OAuth setup.

---

## Phase 5 — Voting

Implement:

* Vote persistence.
* Database uniqueness.
* Eligibility checks.
* Rate limiting.
* Voting UI.
* Anti-abuse foundation.
* Tests.

Document voting behavior.

---

## Phase 6 — Chart Engine

Implement:

* Daily chart generator.
* Chart snapshots.
* Chart entries.
* Ranking algorithm.
* Movement.
* New entries.
* Peak rank.
* Chart history.
* Scheduler integration.
* Tests.

Document ranking behavior.

---

## Phase 7 — YouTube Playback

Implement:

* YouTube video validation.
* Embedded player.
* Top 10 queue.
* Unavailable-video handling.
* Metadata refresh.
* YouTube-related configuration.

Update README.

---

## Phase 8 — Admin

Implement:

* Artist management.
* Song management.
* Genre management.
* Voting controls.
* Moderation.
* Chart tools.
* Suspicious voting review.

---

## Phase 9 — Discovery

Implement:

* Trending.
* Biggest gainers.
* New entries.
* Genre charts.
* Search.
* Featured artists.

---

## Phase 10 — Infrastructure and Deployment

Complete:

* Production Docker image.
* GitHub Actions CI.
* Docker build workflow.
* GHCR publishing.
* Portainer stack.
* Deployment workflow.
* Health checks.
* Rollback documentation.
* Production README.

---

## Phase 11 — Production Hardening

Review:

* Caching.
* Queue reliability.
* Scheduler reliability.
* Security.
* Logging.
* Monitoring.
* SEO.
* Accessibility.
* Backups.
* Privacy.
* Abuse prevention.

---

## Phase 12 — Monetization

Only once product usage warrants it:

* Advertising.
* Sponsorship.
* Promoted content.
* Artist tools.
* Premium analytics.
* Affiliate integrations.

Never couple sponsored payment directly to organic rank manipulation.

---

# How To Approach Every Task

Before coding:

1. Read this `AGENTS.md`.
2. Read `README.md`.
3. Inspect repository structure.
4. Inspect relevant routes.
5. Inspect relevant migrations.
6. Inspect relevant models.
7. Inspect controllers/actions/services.
8. Inspect views/components.
9. Inspect existing tests.
10. Inspect Docker files if infrastructure is relevant.
11. Inspect GitHub Actions if CI/CD is relevant.
12. Determine whether the requested feature partially exists.

Then implement the smallest coherent production-quality change.

Do not duplicate existing functionality.

---

# Repository Inspection

Do not assume framework or dependency versions.

Inspect actual files such as:

```text
composer.json
composer.lock
package.json
package-lock.json
Dockerfile
docker-compose.yml
.env.example
phpunit.xml
vite.config.*
tailwind.config.*
.github/workflows/*
```

Use the project's real versions and conventions.

---

# Dependency Policy

Before adding a package, ask:

1. Does Laravel already provide this?
2. Does an existing package in the repository provide this?
3. Is the proposed package maintained?
4. Is it necessary?
5. What security or operational burden does it add?
6. Would a small amount of straightforward code be better?

Do not casually introduce dependencies.

If adding one, document meaningful operational implications.

---

# Ambiguous Requirements

Do not invent major product behavior.

If ambiguity is low risk, make a reasonable assumption and state it.

Important ambiguities include:

* Vote limits.
* Chart timezone.
* Ranking formula.
* Chart cutoff.
* Anonymous voting.
* Song submission permissions.
* Sponsored placement behavior.

Preserve existing behavior unless instructed otherwise.

---

# Commands and Destructive Operations

Be careful with destructive commands.

Never casually run:

```bash
php artisan migrate:fresh
php artisan db:wipe
DROP DATABASE
TRUNCATE
rm -rf
docker volume rm
docker compose down -v
```

against data that may matter.

Never destroy production data as part of deployment.

Prefer reversible migrations.

---

# Git Discipline

Make focused changes.

Do not:

* Rewrite unrelated code.
* Reformat the entire repository unnecessarily.
* Delete user work.
* Mix unrelated refactors into a feature.
* Commit generated junk.
* Commit secrets.

Keep commits logically coherent when asked to commit changes.

---

# CI/CD Definition of Done

Infrastructure changes are complete only when applicable checks succeed:

* Docker image builds.
* `docker compose config` succeeds.
* Laravel boots.
* Database works.
* Redis works.
* Queue starts.
* Scheduler starts.
* Frontend builds.
* Tests pass.
* CI syntax is valid.
* Image tags are predictable.
* GHCR publishing configuration is correct.
* Portainer can consume the stack via pull-only "Pull and redeploy".
* Application services use the same image tag.
* Migrations run automatically and idempotently on container startup.
* Health checks succeed.
* Deployment can be mapped to a Git release tag (vX.Y.Z).
* Rollback is documented.
* README is current.

---

# Feature Definition of Done

A feature is not complete simply because it renders.

Where applicable, completion means:

* Functional behavior works.
* Server validation exists.
* Authorization exists.
* Database integrity exists.
* Error states exist.
* Mobile layout works.
* Accessibility is reasonable.
* Tests exist.
* N+1 issues are avoided.
* Security was considered.
* Environment variables are documented.
* `.env.example` is updated.
* README is updated.
* Existing tests pass.
* Frontend build succeeds where relevant.

---

# Claude Working Protocol

For every substantial implementation:

## 1. Inspect

Read existing code and documentation first.

Never blindly scaffold over working code.

## 2. Plan

Briefly identify:

* Existing behavior.
* Files that need changes.
* Data/schema implications.
* Testing implications.
* Documentation implications.

## 3. Implement

Make the smallest complete production-quality change.

## 4. Validate

Run relevant commands.

Potential commands:

```bash
docker compose config
docker build .
docker compose up -d
docker compose ps

docker compose exec app php artisan test
docker compose exec app php artisan migrate:status

npm ci
npm run build

./vendor/bin/pint --test
./vendor/bin/phpstan analyse
```

Only run commands actually supported by the repository.

## 5. Fix

Resolve failures caused by the implementation.

Do not knowingly leave tests broken because "the feature is implemented."

## 6. Documentation

Determine whether the change affects README or other documentation.

If yes, update documentation in the same task.

## 7. Summarize

At the end, provide a concise summary covering:

* What changed.
* Important files.
* Database changes.
* Configuration changes.
* Tests created/updated.
* Commands run.
* README/documentation changes.
* Remaining risks or follow-up items.

---

# Do Not Do These Things

Never:

* Download YouTube videos.
* Extract YouTube audio.
* Circumvent YouTube playback.
* Simulate YouTube engagement.
* Confuse site votes with YouTube likes.
* Claim Billboard affiliation.
* Manipulate organic rankings for sponsors.
* Trust frontend authorization.
* Store plaintext secrets.
* Commit `.env`.
* Bake production secrets into Docker images.
* Put complex ranking algorithms in controllers.
* Put business logic in Blade.
* Dynamically recreate historical rankings from mutable present data.
* Expose MySQL publicly without reason.
* Expose Redis publicly.
* Mount Docker socket into the Laravel app.
* Run Docker containers privileged without justification.
* Automatically run destructive migrations.
* Deploy failing builds.
* Depend exclusively on the `latest` image tag.
* Leave web/queue/scheduler on different app versions.
* Leave README instructions knowingly outdated.
* Introduce Kubernetes without explicit justification.

---

# Current Product Priority

Development priority is:

1. Reliable local Docker environment.
2. Core data model.
3. Voting integrity.
4. Reliable ranking engine.
5. Historical charts.
6. Excellent music discovery experience.
7. YouTube playback.
8. Security.
9. Mobile usability.
10. Deployment reliability.
11. SEO.
12. Analytics.
13. Monetization.

The ranking system is the heart of the product.

Treat chart credibility as a critical system requirement.

---

# Final Infrastructure Direction

Unless repository constraints require something different, prefer:

```text
Developer
   |
   v
GitHub Repository
   |
   +-------------------+
   |                   |
   v                   v
Pull Request        main
   |                   |
   v                   v
CI Checks        CI Checks
                       |
                       v
                 Docker Build
                       |
                       v
                      GHCR
                       |
                       v
                Portainer Stack
                       |
          +------------+-------------+
          |            |             |
          v            v             v
        Web/App       Queue       Scheduler
          |
          +----------+
          |          |
          v          v
       Database    Redis
```

This architecture is sufficient for the expected application.

The step from GHCR to the Portainer Stack is an operator-triggered pull ("Pull and redeploy"), not an automatic webhook — the same manual, deliberate deployment model already used for this organization's baby-tracker project. Add webhook automation later only if the team decides the manual step is genuinely a bottleneck.

Prefer boring, understandable infrastructure over unnecessary orchestration.

---

# Final Guiding Principle

Build an independent fan-voted music platform whose value comes from:

* Community participation.
* Trustworthy rankings.
* Historical data.
* Artist discovery.
* Song discovery.
* Useful chart insights.
* Excellent user experience.

YouTube provides playback.

It is not the product itself.

Every technical decision should strengthen the application's independent value while keeping the system understandable, secure, deployable, testable, and maintainable.

When code, infrastructure, or behavior changes, make sure the documentation changes with it.

A feature that works but cannot be reliably understood, tested, deployed, or operated is not finished.
