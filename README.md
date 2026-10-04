# Personal portfolio

A Laravel portfolio with a public homepage, project index, project detail pages, and a private owner CMS.

## Local setup

1. Install PHP, Composer, Node.js, and npm.
2. Install dependencies: `composer install` and `npm install`.
3. Copy `.env.example` to `.env`, set `APP_KEY` with `php artisan key:generate`, and configure `ADMIN_SETUP_KEY` to a long random value.
4. Create a MySQL database named `portfolio` and set its connection values in `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`). Then run `php artisan migrate --seed`.
5. Create the public upload symlink with `php artisan storage:link`.
6. Build assets with `npm run build`, then start Laravel with `php artisan serve`.
7. Open `/admin/setup` and enter the same `ADMIN_SETUP_KEY` to create the first and only owner account. Setup closes once that account exists. Remove the key from the deployment environment after setup.

## Public site

- `/` contains the editable profile, experience, skills, personal sections, featured projects, GitHub profile snapshot, and contact form.
- `/projects` lists published projects with category filters.
- `/projects/{slug}` shows a published project's details; drafts and unknown slugs return 404.
- The contact form stores submissions in the private inbox at `/admin/messages`. It does not send email.
- GitHub profile and repository statistics are fetched in the browser. If the API is unavailable, the page retains its seeded snapshot and identifies it as a snapshot.

## Admin CMS

Sign in at `/admin/login`. The owner can update profile and SEO settings; create, edit, publish, order, or delete projects, experience, skills, personal sections, social links, and navigation; upload public portfolio media; and review, mark, archive, or delete contact messages. Project records remain manually curated rather than imported from GitHub.

The initial portfolio content avoids details that were not verified from the supplied brief or public project sources. Update the editable profile, employment, and CV settings after confirming the source information.

## Visitor analytics and GeoLite2

Anonymous analytics are enabled by default for successful public HTML pages. The privacy notice lets a visitor opt out; opted-out browsers are not tracked. Analytics use first-party random visitor/session IDs and keep detailed records indefinitely. No raw IP address, full user-agent string, contact fields, or arbitrary client metadata are retained.

Approximate country, region, and city are available when a local MaxMind GeoLite2 City database is configured. Create `storage/app/geoip`, obtain the GeoLite2 City database through a MaxMind account, place `GeoLite2-City.mmdb` there, and set `MAXMIND_GEOIP_DATABASE` in `.env` to its path. The database is ignored by source control and should be updated from MaxMind periodically; replace the file atomically so requests never read a partial download. If the file is missing or has no match, location is shown as Unknown. No visitor IP is sent to an external GeoIP service.

The scheduler builds daily analytics rollups just after midnight. Configure the Laravel scheduler to invoke `php artisan schedule:run` every minute. Detailed visits remain available regardless of rollup state.

## Production deployment

Configure `ADMIN_SETUP_KEY` and MySQL credentials through the deployment environment. Run `php artisan migrate --force`, `php artisan storage:link`, and `npm run build`. Use HTTPS, set `APP_DEBUG=false`, and remove the setup key after creating the owner. Back up the database and `storage/app/public` uploads together.

The checked-in/local SQLite database remains available as a backup when switching an existing installation to MySQL. Copy its portfolio, profile settings, owner accounts, messages, and media metadata into the migrated MySQL database before using the MySQL-backed site. Keep the SQLite file until those records have been verified in MySQL.

## Verification

Run the focused Laravel feature suite with `php artisan test --compact` and check compiled Blade views with `php artisan view:cache`. Build production assets with `npm run build`.
