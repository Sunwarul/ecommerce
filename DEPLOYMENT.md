# Deployment guide

Laravel 12 + Inertia (Vue 3, PrimeVue 4) POS / showroom system with installments, bank EMI and SMS.

## Requirements

- PHP 8.2+ with `pdo_mysql`, `mbstring`, `intl`, `gd`, `zip`, `bcmath`, `openssl`
- MySQL 8 (or MariaDB 10.6+)
- Node 20+ (only on the machine that builds assets)
- A web server (Nginx or Apache) pointing its document root at `public/`
- HTTPS (required: session cookies are marked `secure`)

## First install

```bash
git clone <repo> pos && cd pos
composer install --no-dev --optimize-autoloader
cp .env.example .env          # then edit APP_URL, DB_*, MAIL_*, ADMIN_EMAIL
php artisan key:generate

npm ci && npm run build        # builds public/build

php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force   # permissions + Super Admin role
php artisan db:seed --class=UserTableSeeder --force        # first admin (prints a random password if ADMIN_PASSWORD is empty)
php artisan db:seed --class=PaymentMethodSeeder --force    # optional: Cash, bKash, Nagad, Bank…

php artisan storage:link
php artisan optimize           # caches config, routes, views, events
```

`php artisan db:seed` (the full `DatabaseSeeder`) also inserts **demo products and customers** — don't run it on a live shop.

Both `RolePermissionSeeder` and `UserTableSeeder` are safe to re-run: they only add what's missing and never delete roles, permissions or users.

## Background processes

### Scheduler (required)

Late fees and installment due/overdue SMS run daily at 09:00 (`installments:process`).

```cron
* * * * * cd /var/www/pos && php artisan schedule:run >> /dev/null 2>&1
```

### Queue worker (recommended)

SMS is sent right after the response by default (no worker needed). If you switch **SMS Settings → Delivery mode** to *Queue*, run a worker, for example with Supervisor:

```ini
[program:pos-queue]
command=php /var/www/pos/artisan queue:work --sleep=3 --tries=1 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/pos/storage/logs/queue.log
```

## File permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
```

## Updating

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force   # picks up newly added permissions
php artisan optimize
php artisan queue:restart
php artisan up
```

## Security checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_URL` uses `https://` (the app then forces HTTPS links)
- [ ] `USER_REGISTRATION_ALLOWED=false` unless you really want public sign-up (self-registered users get **no** permissions)
- [ ] Change the first admin's password after logging in (Profile)
- [ ] Give staff roles with only the permissions they need (Users → Roles). Super Admin bypasses every check.
- [ ] Database user has rights on this database only
- [ ] Daily database + `storage/app/public` backups (e.g. `mysqldump` + rsync)
- [ ] SMS gateway: send a test SMS from **SMS → Settings → Gateways** before going live

## Access control

Every `/admin/*` route needs a permission (`app/Http/Middleware/AuthorizeRoutePermission.php`). CRUD routes map to `"<Resource> <Ability>"`, e.g. `taxes.store → "Tax Store"`. A route that isn't mapped is denied by default, and `tests/Feature/Security/RoutePermissionTest.php` fails if one is added without a mapping.

## Languages & RTL

UI languages: English, বাংলা, العربية (RTL), Español, हिन्दी.

- Frontend strings: `resources/js/locales/<lang>.json` (shared keys) and `resources/js/locales/<lang>/<area>.json` (one file per feature area). English is the fallback.
- Server messages and validation: `lang/<lang>.json` and `lang/<lang>/*.php`.
- Translations other than English were machine-assisted; have a native speaker review them before launch.

## Health check

`GET /up` returns 200 when the app boots (use it for uptime monitoring / load balancers).

## Tests

```bash
php artisan test
```

The suite runs on in-memory SQLite (see `phpunit.xml`).
