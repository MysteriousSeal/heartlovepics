# Changelog

All notable changes to this project are documented here, grouped by release. Versions
correspond to the `version` value in `config/app.php` (also shown in the site footer and at
[`/changelog`](resources/views/home/changelog.blade.php), which carries a softer,
end-user-facing rewrite of the same releases). This file instead follows the commit
history directly, so entries are more technical. Format loosely follows
[Keep a Changelog](https://keepachangelog.com/); dates are UTC commit dates.

## [1.0.30] - 2026-08-13

### Added
- Public Artists and Parodies browse pages (`/artists`, `/parodies`), sortable A–Z or by
  most posts — same layout as the existing Tags page (`b23d4ae`)

### Changed
- Parody cover images now crop to a 2:3 portrait (800×1200) instead of 9:16, in the
  admin uploader and on the public parody page (`b23d4ae`)

## [1.0.29] - 2026-08-13

### Added
- Admin API route to list tags together with their post IDs (`dee186f`)
- Admin API route to delete a tag (`863ffa3`)
- Admin API route to list all posts, 50 per page (`3f8574b`)
- Parody field on posts, with a linked gallery page listing every post sharing that
  parody (`f63fc69`)
- Parody field exposed in the admin image API (`b0d4748`)
- Parody admin CRUD — cover image, description, and a pending-review queue for
  free-text parody names not yet turned into a profile (`35aa7f5`)
- Parodies listed in the sitemap (`538c134`)

### Changed
- Parody covers are center-cropped to a 9:16 portrait and fill their container width
  (`538c134`)

### Documentation
- Documented artist pages, the contact inbox, admin analytics/configuration screens, and
  the `engagement:seed-random` / `cron-logs:prune` scheduled commands in the README (`20e3410`)
- Added this CHANGELOG.md, derived from git and version-bump history (`6209cfb`)

## [1.0.28] - 2026-08-12

### Changed
- Rewrote the homepage About section and footer copy; refreshed the Terms of Use and
  Privacy Policy to match current site behavior (`680aafb`)
- Bumped the all-time visit count range (10–20) added per run by the engagement-seeding
  cron job (`8d997f9`)

### Fixed
- Hid cron-triggered events by default in the admin activity log (`fce3b3e`)

## [1.0.27] - 2026-08-12

### Added
- User activity timeline in admin, split out into its own "Admin Activity" view (`200647b`)
- Bookmark activity surfaced in the admin timeline (`5ff7575`)

### Changed
- Admin activity timeline shows avatars and clearer guest labeling (`8b6833e`)
- Widened the second column in the admin activity grid (`13472a6`)
- Adjusted masonry card footer image-stats layout and vertical spacing (`2b33611`, `f46fc7a`)

Released via `938a770` ("Release v1.0.27 and update changelog").

## [1.0.26] - 2026-08-12

### Added
- User activity timeline added to admin, split out from Admin Activity (`200647b`)

## [1.0.25] - 2026-08-12

### Added
- Summary stats row on the admin analytics page (`c546822`)

### Changed
- Polished the mobile menu and homepage KPI bar (`341eb37`)
- Added long-lived `Cache-Control` headers for static assets (`99a5ea5`)

### Fixed
- Fixed a broken desktop header layout / duplicate search bar (`341eb37`)

## [1.0.24] - 2026-08-12

### Added
- Live active-visitor count and unanswered-message badges in the admin nav (`007004f`)

### Fixed
- Fixed duplicate cron log listeners (`cb0d914`)
- Capped synthetic seed likes at 1–2 total per run (`a751248`, `cb0d914`)

## [1.0.23] - 2026-08-11

### Added
- Admin cron log page and the scheduled engagement (`engagement:seed-random`) job (`a443f5a`)
- Full contact reply history stored and displayed in admin (`ebc743e`)

### Changed
- Switched contact-reply email delivery to PHP's native mailer and polished the reply
  email templates (`3784d71`)
- Forced the contact-reply `From` and `Return-Path` headers to the configured address (`99135cd`)

## [1.0.22] - 2026-08-11

### Added
- Contact page and admin email replies for contact messages (`1af0f68`)
- Contact form and an admin messages inbox with archive tabs (`94ab3cf`)

### Changed
- Show the unique-visitor total in the analytics range summary (`852d0bc`)

## [1.0.21] - 2026-08-11

### Added
- Country donut chart on the analytics page (`02eaa64`)
- "Made from scratch with love" footer credit line (`02eaa64`)

## [1.0.20] - 2026-08-11

### Changed
- Separated bots from guests in analytics visitor counts (`f92af7a`)
- Limited the public `/changelog` page to end-user-facing notes only (`7b31d8d`)

## [1.0.19] - 2026-08-11

### Added
- Unique users and guests shown in the analytics range summary (`19e812b`)

## [1.0.18] - 2026-08-11

### Added
- Active-visitor count on admin analytics (`ec4a6b6`)

## [1.0.17] - 2026-08-11

### Added
- Analytics donut charts and time-range filters (`0b8ddd9`)

## [1.0.16] - 2026-08-11

### Changed
- Expanded changelog bullets into individual entries and fixed tag text layout on the
  public changelog page (`ceac79e`)

## [1.0.15] - 2026-08-11

### Changed
- Polished the admin images grid; artist credits shown on admin image cards (`0b0264e`)

## [1.0.14] - 2026-08-11

### Changed
- Polished the admin dashboard, activity, users, and artists pages (`d019cee`)

## [1.0.13] - 2026-08-11

### Changed
- Enriched admin analytics and polished the admin UI (`a69cd04`)

## [1.0.12] - 2026-08-11

### Changed
- Clear follows and notifications when an admin deletes an empty user account (`0e4eda3`)

## [1.0.11] - 2026-08-11

### Added
- Admins can delete empty user accounts (`fe4853a`)

## [1.0.10] - 2026-08-11

### Added
- Description status shown on admin image cards (`126ed5e`)

## [1.0.9] - 2026-08-11

### Changed
- Tightened admin analytics table columns (`7dc1960`)

## [1.0.8] - 2026-08-11

### Added
- Admin visit analytics (`119c052`)

### Changed
- Aligned the tags page width with the site header (`119c052`)

## [1.0.7] - 2026-08-11

### Added
- Artist descriptions and a Visual/HTML description editor mode (`68d043b`)

### Changed
- Polished artist pages into a two-column layout (avatar/links/post count on the left,
  description on the right) (`68d043b`)

## [1.0.6] - 2026-08-11

### Added
- Artist post counts shown on gallery cards (`b16a2dc`)
- Admin API route to fetch full post details by ID or slug (`8959724`)

## [1.0.5] - 2026-08-11

### Added
- Artist avatars, shown next to artist credits on posts and on gallery cards (`6663ab1`)
- Gallery author/artist display toggles (site settings) (`6663ab1`)

## [1.0.4] - 2026-08-10

### Added
- Users can change their username from the profile edit page, with live availability
  checking (`fd0ea34`)

### Changed
- Restricted image uploads to admins (`c994021`)

## [1.0.3] - 2026-08-10

### Changed
- Redesigned the tags index, Privacy Policy, and Terms of Use pages (`75e7352`)
- Tags index gained A–Z grouping and ranked "most used" cards (`75e7352`)

## [1.0.2] - 2026-08-10

### Changed
- Redesigned the changelog page as a timeline with release cards and a current-version
  badge (`63773e4`)

## [1.0.1] - 2026-08-10

### Added
- Public `/changelog` page (`3c3a943`)
- Version number displayed in the site footer (`3c3a943`)

## [1.0.0] - 2026-08-10

### Added
- Initial commit (`abe54cc`)
