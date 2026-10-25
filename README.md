# BD SOURCING HUT — Laravel Luxury Apparel Sourcing Platform

A **real Laravel 12 source project**, with a Blade-based cinematic public website, a database-backed administration interface, inquiry handling, editorial images and 3D motion enhancements.

> **Important validation note:** This project was authored and PHP/JavaScript syntax-checked in a restricted environment. Composer and several required PHP extensions were not available, so dependencies, migrations, HTTP tests and a running browser session **could not be executed here**. Install and run the verification commands below before treating it as production-deployed. Do not interpret source generation as proof that the application is fully deployed or integration-tested.

## Requirements

- PHP **8.2+** (PHP 8.3 or 8.4 is suitable for supported Laravel 12 versions), Composer 2.x.
- PHP extensions: `mbstring`, `pdo_mysql` (or `pdo_sqlite` for tests), `openssl`, `fileinfo`, `ctype`, `tokenizer`, `xml`, `dom`, and `curl`. Image manipulation GD is optional; upload metadata uses PHP `getimagesize`.
- MySQL 8+ or compatible MariaDB, with an empty dedicated database and least-privileged database user.
- Apache or Nginx, document root set to the project's **public/** directory.
- Internet access to install PHP dependencies and fetch optional Google Fonts / GSAP CDN scripts. The site CSS and its baseline interactions are bundled locally.

## Install (Windows PowerShell / macOS / Linux)

1. Extract the ZIP, then open a terminal in the **bd-sourcing-hut** directory.
2. Run:

   ```bash
   composer install
   ```

3. Create environment settings from the sample:

   ```bash
   cp .env.example .env
   ```

   On Windows PowerShell use `Copy-Item .env.example .env` instead.

4. Generate an application encryption key:

   ```bash
   php artisan key:generate
   ```

5. Create an empty MySQL database (e.g. `bd_sourcing_hut`). Edit `.env`:

   ```dotenv
   APP_URL=http://localhost:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bd_sourcing_hut
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

6. Run migrations and insert **editable demo content**:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

7. Create the first administrator **interactively**; no password is included in this ZIP:

   ```bash
   php artisan app:create-admin
   ```

8. Start a local server:

   ```bash
   php artisan serve
   ```

   Public site: `http://localhost:8000/`. Admin login: `http://localhost:8000/admin/login`.

**If using Windows CMD** replace `cp .env.example .env` with `copy .env.example .env`.

## Modules

| Module | Public routes | Admin routes |
|---|---|---|
| Home and page builder | `/` | `/admin/homepage`, `/admin/page-sections/{slug}` |
| About and pages | `/about`, `/pages/{slug}` | `/admin/pages`, section editor |
| Six seed services + expandable catalog | `/services`, `/services/{slug}` | `/admin/services` |
| Eight seed collections + expandable catalog | `/collections`, `/collections/{slug}` | `/admin/collections` |
| Contact inquiries | `/contact` | `/admin/inquiries` |
| Media and images | `/storage/...`, bundled `/images/...` | `/admin/media` |
| Menus, footer, settings, theme | Five-item primary menu | `/admin/menus`, `/admin/settings`, `/admin/footer`, `/admin/theme` |
| Process steps | Homepage production timeline | `/admin/process` |
| User security | `/admin/login` | `/admin/users`, password reset |
| Sitemap / metadata | `/sitemap.xml`, `/robots.txt` | SEO fields on content + `/admin/seo` |

### Editing the homepage

Visit **Admin → Homepage Builder**. Each section has layout variant, heading, body copy, link, images (including optional mobile hero), image focus, hero overlay, publication visibility and integer order. To preview a section before showing it publicly: uncheck *Visible*, save, click *Preview*, then make it visible when ready.

### The visual images

`public/images/` includes **24 generated JPG photos/compositions plus 24 compressed WebP derivatives** distributed among hero, about, services, collections, production and contact. These are **AI-generated editorial visuals, not photographs of verified BD SOURCING HUT employees, facilities, certifications, clients or orders**. They may be replaced at any time using Admin → Media Library, then choosing the corresponding image in the editor. The seed data only references local existing JPGs; nothing depends on hotlinked placeholder URLs.

The generated compositions originated as multiple separately created editorial contact sheets, then were cropped and art-directed into individual JPGs. Their pixel dimensions are provided for layout consistency; cropping/upscaling does not imply they were natively generated at those exact dimensions. For an official client launch, replace the sample images with approved, authentic company photography as appropriate.

Uploads accept JPG, PNG, and WebP, up to 10 MB each, and are saved to `storage/app/public/media/...`. Image records store MIME type, size, dimensions, alternate text, and category. Replacing an image preserves its database ID; references update automatically. Deletion is blocked for images still referenced by website content.

### Managing product galleries

Open Collections → Edit collection. Select gallery image checkboxes, drag them into order, and press **Save changes**. Gallery order and membership are stored in `collection_images`.

### Business inquiries

All contact submissions are server-validated, CSRF-protected, database-stored and rate-limited. The admin inbox supports status changes, notes, filters and CSV export. Submitted inquiries are **not exposed** publicly.

For optional notification email, configure a working SMTP mail transport in `.env` and add:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mail.example.org
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-secret
MAIL_FROM_ADDRESS=your-approved-sender@example.org
INQUIRY_NOTIFICATION_EMAIL=private-inbox@example.org
```

**Do not use guessed company contact details**. Set company email, telephone, address and social profiles in the dashboard before launch. No company performance figures, awards, partners, clients or certifications have been fabricated in the seed data.

## Frontend assets and animation build

The project uses **custom CSS** and **vanilla JavaScript** at `public/assets/site.css`, `public/assets/site.js`, `public/assets/admin.css` and `public/assets/admin.js`, served as prebuilt static assets. **No Node.js bundling step is required**. To change code-level styles, edit these files directly; for theme colors, font selection and motion strength, use Admin → Visual Theme. The website integrates GSAP 3.14 and ScrollTrigger as progressive enhancements through a versioned CDN. If the CDN is unavailable, baseline navigation, gallery switching and touch-friendly layouts remain functional, and reveal animations gracefully become static content. Respect for `prefers-reduced-motion` is built in.

For a stricter offline deployment, download authorized GSAP distribution files, serve them from `public/assets/vendor/`, and update the two script URLs in `resources/views/frontend/layouts/app.blade.php` and the corresponding `Content-Security-Policy` in `app/Http/Middleware/SecurityHeaders.php`.

## Verification before launch

```bash
php artisan route:list
php artisan view:cache
php artisan config:cache
php artisan test
```

Tests in `tests/Feature/SiteFeatureTest.php` use in-memory SQLite, so enable `pdo_sqlite` when running the suite. Independently verify each service and collection route, published/draft permissions, safe image upload/replacement, gallery order, form submission, mail delivery, password reset, image references and responsive layouts at 360, 390, 430, 768, 1024, 1440 and 1920 px. Verify in at least two browser engines; these live-browser checks were not possible during generation.

If a code change occurs, clear Laravel's caches during development: `php artisan optimize:clear`.

## Production deployment

1. Use PHP 8.2+ with required extensions and a production-ready web server. Point its document root to **public/**, not the repository root.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-actual-domain` and a generated secret `APP_KEY` in secure environment settings. Never commit `.env` or passwords.
3. Give writable permissions only to Laravel's `storage/` and `bootstrap/cache/` directories as required by your hosting platform.
4. Install dependencies with `composer install --no-dev --optimize-autoloader` and secure the resolved Composer lockfile in your deployment pipeline.
5. Run `php artisan migrate --force` and `php artisan storage:link`, then `php artisan optimize`. Seed only on the intended initial setup; do **not** repeat the seeder to reset or overwrite real business data.
6. Set up TLS, backups (database and uploaded images), PHP upload limits, log retention and SMTP delivery; configure database credentials with minimum permissions.
7. Restrict administrator accounts to trusted users and test recovery emails; enforce strong passwords and consider upstream MFA/SSO for sensitive deployments.
8. Run integration tests and QA review, including CSP adjustments if using additional approved services, before switching DNS or advertising launch.

A systemd/Nginx environment can serve this Laravel application directly; no separate SPA runtime or API service is necessary. For shared hosting, ensure the hosting provider supports Laravel webroot and `storage:link`.

## Notes and limits

- Administrator privileges are a secure **single-role boolean** (`is_admin`) rather than a multi-role enterprise permission engine.
- Client-side 3D uses CSS perspective, layered motion, hover depth and GSAP. No heavyweight continuous WebGL renderer is loaded, which improves mobile performance. This is intentional under the specification's Three.js *where appropriate* wording.
- WYSIWYG arbitrary HTML editing is intentionally omitted: page body text is plain text to avoid stored XSS risks. All public output is escaped, except headings explicitly escaped before adding line breaks.
- The seed descriptions are **illustrative editable content**, not verified claims about the real company.
- This package intentionally excludes Composer's `vendor/` folder and a `composer.lock` file because network access was unavailable in the build environment. The application therefore needs `composer install` on a configured PHP environment before it can run.
- When revising content, keep the five default primary top-level links consistent with the navigation specification; dropdown entries are managed separately.





--php artisan app:create-admin
-- munna7e@gmail.com
-- munna7e@gmail.com
