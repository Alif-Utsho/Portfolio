# Personal Portfolio and Owner CMS

A Laravel application for a software engineer’s public portfolio, with a private owner-only content manager and first-party visitor analytics. Portfolio content is curated in the CMS; GitHub repositories are not imported.

## What the application includes

### Public portfolio

- Responsive homepage with profile, experience, skills, selected work, personal sections, social links, and contact form.
- `/projects` index with category filters and pagination.
- `/projects/{slug}` detail pages for published projects. Drafts and unknown slugs return 404.
- Light and dark themes, responsive navigation, reduced-motion support, and page-specific SEO metadata.
- Live GitHub profile/repository statistics fetched from GitHub in the browser. The seeded or last available snapshot remains available when GitHub cannot be reached.
- Contact submissions are validated, rate-limited, and saved to the private admin inbox. The form does not send email.

### Private owner CMS

- One owner account, with first-account setup protected by `ADMIN_SETUP_KEY`. Setup closes after an owner is created; there is no public registration or staff-role system.
- Editable site/profile and SEO settings.
- Create, update, publish, unpublish, order, and delete projects, experience, skills, personal sections, social links, and navigation links.
- Public-disk image uploads for projects and personal sections. Deleting an image clears its content references; affected sections fall back to their default artwork.
- Contact inbox with read/unread/archive status management.
- Responsive admin dashboard, sidebar drawer, unread-message indicator, and mobile-friendly content forms.

### Visitor analytics

- Tracks successful public HTML page views and a fixed set of interactions: project views, repository/demo/social/navigation/email/CV clicks, contact-section views, form starts, contact submissions, CV downloads, and page duration.
- Admin, system, failed, non-HTML, and detected bot requests are excluded from page-view tracking.
- Analytics are enabled by default. Visitors can opt out using the privacy notice or footer privacy control.
- Uses random first-party visitor/session IDs. A returning visitor is one whose visitor ID was seen previously. Sessions expire after 30 minutes of inactivity; the live view treats activity within two minutes as online.
- Stores referrer domains and validated UTM fields. Page paths are stored without arbitrary query strings.
- Does not store raw IP addresses, raw user-agent strings, contact form fields in analytics, or arbitrary client metadata. It stores derived device/browser/OS labels and, when configured, approximate location.
- Detailed visitor, session, page-view, and event records are retained indefinitely. Daily rollups support summaries; dashboard summaries are briefly cached.
- Optional country/region/city lookup uses a local MaxMind GeoLite2 City database. No visitor IP is sent to a GeoIP API. Missing database files or unmatched addresses display as unknown.
- Analytics reports include visitor/session/page-view totals, new/returning visitors, bounce rate, pages per session, duration, devices, browsers, operating systems, sources, geography, popular pages/projects, downloads, and contact milestones. Authenticated CSV exports support session, page, and event data.

## Technology

- PHP 8.3 or later; Laravel 13.
- MySQL for local and production application data. PHPUnit uses isolated in-memory SQLite as configured in `phpunit.xml`.
- Blade templates, handwritten CSS, and vanilla JavaScript modules.
- Vite 8, Laravel Vite plugin, and Tailwind CSS 4 Vite integration.
- MaxMind GeoIP2 PHP library for local GeoLite2 database reads.
- PHPUnit 12 for feature and unit tests; Laravel Pint for PHP formatting.

## Requirements

- PHP `^8.3` with the extensions required by Laravel and the configured MySQL driver.
- Composer 2.
- Node.js and npm.
- MySQL 8-compatible server (Laragon’s MySQL works for local development).
- Optional: MaxMind GeoLite2 City `.mmdb` file for approximate location analytics.

## Local setup

1. Install PHP, Composer, Node.js/npm, and start MySQL.
2. Install dependencies:

   ```sh
   composer install
   npm install
   ```

3. Create a local environment file and application key:

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp` if needed.

4. Create a MySQL database named `portfolio` (or choose another name) and configure the database variables in `.env`:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=portfolio
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Set a long random setup key in `.env` before creating the owner:

   ```dotenv
   ADMIN_SETUP_KEY=replace-with-a-long-random-secret
   ```

   Set `PORTFOLIO_GITHUB_USERNAME` to the GitHub username shown in the live profile panel if it differs from the default.

6. Run migrations and seed the starter portfolio content:

   ```sh
   php artisan migrate --seed
   ```

   The seeder creates public portfolio entries and site settings, but does not create an admin account.

7. Expose uploaded portfolio media and build frontend assets:

   ```sh
   php artisan storage:link
   npm run build
   ```

8. Start the app:

   ```sh
   php artisan serve
   ```

   In another terminal, use `npm run dev` while editing frontend assets.

9. Visit `/admin/setup`, enter the configured key, and create the owner account. Sign in at `/admin/login`. Remove `ADMIN_SETUP_KEY` from the environment after setup, then clear/rebuild cached configuration if applicable.

Do not use `migrate:fresh` on a database with content you want to keep; it drops existing tables.

## Environment configuration

| Variable | Purpose |
| --- | --- |
| `APP_ENV`, `APP_DEBUG`, `APP_URL` | Laravel environment, error display, and canonical application URL. Use `APP_DEBUG=false` in production. |
| `APP_KEY` | Encryption key generated with `php artisan key:generate`. |
| `ADMIN_SETUP_KEY` | One-time secret required to create the first owner account. Remove it after setup. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | MySQL connection. |
| `PORTFOLIO_GITHUB_USERNAME` | GitHub username for the public repository/profile panel. |
| `MAXMIND_GEOIP_DATABASE` | Path to the local GeoLite2 City database. Defaults to `storage/app/geoip/GeoLite2-City.mmdb`. |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Laravel session, cache, and queue backends. The example environment uses database-backed stores. |

Keep `.env` and GeoLite database files private; neither belongs in source control. Never commit real secrets.

## Routes and workflows

| URL | Access | Purpose |
| --- | --- | --- |
| `/` | Public | Profile homepage and contact form. |
| `/projects` | Public | Published project index. |
| `/projects/{slug}` | Public | Published project details. |
| `/contact` | Public POST | Validates and saves a contact message; limited to five submissions per minute per requester. |
| `/admin/setup` | First-owner setup only | Creates the first owner when no owner exists and the setup key is configured. |
| `/admin/login` | Public login | Owner login; login attempts are rate-limited. |
| `/admin` | Owner | Dashboard and live visitor count. |
| `/admin/content/{type}` | Owner | Manage supported portfolio content types. |
| `/admin/settings` | Owner | Manage profile/site settings, contact details, CV/GitHub links, and SEO. |
| `/admin/media` | Owner | Upload and manage portfolio images. |
| `/admin/messages` | Owner | Review and manage contact submissions. |
| `/admin/analytics` | Owner | Analytics overview, visitors, sources, pages, events, geography, real-time visitors, and CSV exports. |

Named routes are defined in `routes/web.php`. Admin content types are listed in `config/portfolio.php` and validated by the content routes and request class.

## Managing portfolio content

- **Site settings:** `/admin/settings` stores the site title/description, hero copy, about copy, location, CV URL, GitHub URL, contact email, and SEO title/description.
- **Projects:** create curated entries with a unique slug, summary/body, technologies, category, repository/primary URL, optional demo URL, publication state, featured flag, sort order, and optional cover image. Featured projects appear first on the project index and in the homepage selected-work section. GitHub repositories are not imported automatically.
- **Experience:** store role, organization, period, summary/body, technologies, publication state, and sort order.
- **Skills:** skill titles are grouped using the group field and sorted for display.
- **Beyond code/personal:** add personal-interest cards with eyebrow, title, summary, and optional image.
- **Social and navigation links:** each entry has a label, URL, publication state, and sort order.
- **Media:** images are stored on Laravel’s `public` disk under `storage/app/public/portfolio`. Supported uploads are JPEG, PNG, and WebP up to 5 MB. Public URLs require the storage symlink. Deleting an image detaches it from any project or personal section that used it.
- **Contact messages:** messages are stored in the database and visible only in the authenticated admin inbox. No outgoing email transport is configured by this application.

Starter content is defined in `database/seeders/PortfolioSeeder.php`. Review it before reseeding an existing site; it uses `firstOrCreate` for portfolio entries and settings.

## Analytics operations

### GeoLite2 setup

1. Obtain the GeoLite2 City database from MaxMind under its applicable account/license terms.
2. Put `GeoLite2-City.mmdb` in `storage/app/geoip/` (or set `MAXMIND_GEOIP_DATABASE` to another local path).
3. Keep the `.mmdb` file out of source control. Replace it periodically using an atomic file replacement so requests never read a partial file.
4. If the file is missing, unreadable, or has no match, the analytics location is unknown; page tracking continues.

### Scheduled commands

The scheduler is defined in `routes/console.php`:

- `analytics:rollup` runs daily at 00:15.
- `analytics:sessions:close` runs every minute to close inactive sessions.

In production, configure cron or the host scheduler to run Laravel’s scheduler every minute:

```cron
* * * * * cd /path/to/portfolio && php artisan schedule:run >> /dev/null 2>&1
```

Inspect scheduled tasks with `php artisan schedule:list`. Detailed records are not automatically purged.

## Project structure

```text
app/
  Console/Commands/        Analytics aggregation and session maintenance
  Http/Controllers/        Public pages and owner admin endpoints
  Http/Requests/           Validation and authorization for form requests
  Http/Middleware/         Admin gate and public analytics tracking
  Models/                  Portfolio, settings, messages, media, and users
  Services/                Analytics recording and reporting
config/                    Portfolio and analytics configuration
database/
  migrations/              Schema for Laravel, portfolio, and analytics data
  seeders/                 Starter portfolio content
resources/
  css/                     Public and admin stylesheets
  js/                      Public interactions/analytics and admin UX
  views/                   Blade templates for public and admin pages
routes/                    HTTP routes and scheduled commands
storage/app/public/        Uploaded public portfolio assets (runtime data)
tests/                     PHPUnit feature and unit tests
```

Analytics use a separate `analytics_sessions` table name to avoid conflicting with Laravel’s standard `sessions` table. The database also includes visitors, page views, events, and daily rollups. Portfolio content is stored in `portfolio_items`; editable site values are in `portfolio_settings`.

## Coding conventions

- Follow the existing Laravel/PHP style: PSR-12 formatting, four-space indentation, typed method arguments/return values where practical, `PascalCase` classes, and `camelCase` methods/variables. Run Pint before submitting PHP changes.
- Keep controllers focused on request orchestration. Put request validation and authorization in Form Request classes; put reusable analytics/query logic in `PortfolioAnalytics` or a suitable service.
- Use Eloquent models for portfolio records and named routes instead of hard-coded internal URLs where practical. Use migrations for schema changes; do not edit an applied migration to change a production schema.
- Escape rendered content through normal Blade `{{ }}` output. Use `@csrf` for forms and Laravel validation errors for feedback.
- Keep project records manually curated. Do not silently import GitHub repositories or present unverified profile history as fact.
- Analytics event names must remain on the allowlist in `PortfolioAnalytics::EVENTS`. Do not send contact form fields, raw IPs, raw user-agent strings, or arbitrary browser properties into analytics.
- Keep public and admin styles/scripts in their corresponding `resources/css` and `resources/js` files. Avoid adding frontend dependencies for small interactions; the current public/admin behavior uses vanilla JavaScript.
- If adding a content type, update `PortfolioItem::TYPES`, `config/portfolio.php`, route constraints, validation, admin navigation, and the relevant display/editor views together.
- Add feature coverage for behavior changes, especially authorization, validation, content publication, contact storage, and analytics privacy.

## Tests and validation

Tests run against in-memory SQLite as configured in `phpunit.xml`, independently of the local MySQL database:

```sh
php artisan test --compact
```

Other useful checks:

```sh
vendor/bin/pint --test
php artisan view:cache
php artisan view:clear
npm run build
```

`php artisan view:cache` checks Blade compilation; clear the generated view cache when finished during local development. `npm run build` compiles the production CSS and JavaScript into `public/build`.

## Production deployment and backups

1. Configure MySQL, `APP_URL`, a production `APP_KEY`, `APP_ENV=production`, and `APP_DEBUG=false` through the deployment environment.
2. Run `composer install --no-dev --optimize-autoloader`, `npm ci`, and `npm run build` in the deployment build process.
3. Deploy code, then run `php artisan migrate --force` and `php artisan storage:link`.
4. Create the owner through the one-time setup route over HTTPS, then remove `ADMIN_SETUP_KEY` and refresh cached configuration.
5. Configure the scheduler, writable Laravel `storage`/`bootstrap/cache` directories, and HTTPS. Back up the MySQL database and uploaded `storage/app/public` assets together.

For an existing SQLite installation, keep the SQLite file as a backup while migrating records and verifying IDs, content, settings, inbox messages, and media metadata in MySQL. Uploaded files themselves live in storage and must be carried over separately. Do not delete the SQLite backup until the MySQL copy and public media URLs have been checked.

## Security and privacy notes

- Never expose `/admin` routes without the existing authentication/admin middleware.
- Protect `ADMIN_SETUP_KEY`, `APP_KEY`, MySQL credentials, and MaxMind account credentials.
- Use HTTPS in production so authentication and analytics cookies are secure.
- Public contact data is retained in the admin inbox; it is excluded from analytics events.
- The application does not send contact submissions by email. Configure and verify a mail transport before adding any email-delivery claims or workflows.
- Analytics data is detailed and retained indefinitely by default. Set and document a retention policy before introducing any cleanup process.
