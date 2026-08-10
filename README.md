# HeartLovePics

A quiet, image-first gallery built with Laravel — upload photo stories, browse by tag or
popularity, like/bookmark/collect what you love, follow creators, and comment. Adult (NSFW)
content is supported behind an age-confirmation gate and is blurred by default.

## Features

**Gallery & discovery**
- Masonry gallery sortable by latest, most viewed, most liked, or random, plus a "Following"
  feed for signed-in users
- Full-text-ish search across titles/descriptions, and a tag index with per-tag pages
- Two selectable post-detail layouts (stacked / side-by-side), saved per-visitor in
  `localStorage`
- Word-boundary + popularity-ranked tag suggestions while composing a post

**Social**
- Likes (works for guests too, via a hashed IP+session fingerprint — no account required),
  bookmarks, collections, follows, comments (with nested replies and @mentions), profile
  "shouts," and personal journals
- In-app notifications for likes, comments, bookmarks, follows, replies, and mentions

**Publishing**
- Multi-image posts (one cover + additional images), drag-to-reorder, alt text, tags,
  drafts, private posts
- Soft-delete with an undo toast — deleted posts are recoverable for a grace period before
  being permanently pruned
- NSFW flagging with content warnings and a client-side blur + age-confirmation gate

**Admin**
- Separate `/admin` panel (image moderation, comment moderation, user management/bans,
  activity log, site dashboard)
- All moderation actions are recorded to an internal activity log

**SEO**
- Per-view meta titles/descriptions, canonical URLs, `robots` control (low-value views like
  Random/Following/Search are `noindex`), Open Graph/Twitter cards, XML sitemap, and
  JSON-LD structured data — deliberately scoped to exclude NSFW content from structured
  data and social-preview images so it's never surfaced outside the site's own age gate

## Tech stack

- **Backend:** PHP 8.3+, Laravel 13
- **Frontend:** Server-rendered Blade + vanilla JS (no SPA framework), Vite + Tailwind for
  the asset pipeline
- **Database:** SQLite by default (swap via `.env`, standard Laravel database config)
- **Storage:** local disk (`storage/app/public`, served via the `public/storage` symlink)

## Getting started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite   # if using the default SQLite setup
php artisan migrate

php artisan storage:link
php artisan db:seed               # optional: creates a demo admin account + sample data

npm run build                     # or `npm run dev` while developing
php artisan serve
```

Or use the composer scripts that wrap the same steps:

```bash
composer run setup   # install, .env, key:generate, migrate, npm install/build
composer run dev     # serve + queue:listen + pail (logs) + vite, all at once
```

The app expects PHP 8.3+, and the queue/session/cache drivers default to the database
driver, so the standard `migrate` step covers everything (no separate Redis setup needed
for local dev).

Posts support up to 100 images in one upload, so `php.ini` needs `max_file_uploads >= 100`
(PHP's default is 20) — otherwise PHP silently drops the extra files before the request
even reaches Laravel. Also check `upload_max_filesize` and `post_max_size` are large enough
for that many files at once. Local dev uses `php-local.ini` via `./serve.sh`
(`max_file_uploads=120`, `post_max_size=1200M`). Prefer `./serve.sh` over bare
`php artisan serve`, which can ignore the custom ini on its child process.

### Default admin account

`AdminSeeder` (run via `php artisan db:seed`) creates/updates a `username: admin` account
with `is_admin: true`. **Change its password immediately after seeding** — sign in at
`/admin` and update it, or edit the seeder to pull the password from an environment
variable before running it anywhere beyond a local sandbox. Admin login is separate from
the regular `/login` form and lives at `/admin`.

## Scheduled / maintenance commands

| Command | Purpose |
|---|---|
| `php artisan images:prune-trashed` | Permanently deletes soft-deleted images (and their files) past the undo grace period (`--hours`, default 24). Scheduled hourly. |
| `php artisan view-events:prune` | Deletes image view-event rows older than the retention window (`--days`, default 30). Scheduled daily. |
| `php artisan images:generate-thumbnails` | Backfills thumbnails and dimensions for existing images. Run manually as needed (e.g. after importing images outside the normal upload flow). |

Run `php artisan schedule:work` (or set up a real cron entry for `schedule:run`) in
production so these actually fire.

## Content policy

This project hosts user-submitted content, including adult (NSFW) material, gated behind
an 18+ self-confirmation. See [`/terms`](resources/views/home/terms.blade.php) and
[`/privacy`](resources/views/home/privacy.blade.php) for the full policy and data-handling
details if you're deploying this for real users.

## License

No license file is currently included — treat this repository as all-rights-reserved
unless/until one is added.
