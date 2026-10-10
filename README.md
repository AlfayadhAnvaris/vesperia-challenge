# Vesperia Operational Risk Reporting

Laravel application that imports the provided `submission.json` form definition, stores its schema and submitted answers as JSON, exposes them through APIs, and renders a schema-driven web form.

## Requirements

- PHP 8.1+ and Composer
- MySQL/MariaDB for the application
- Node.js and npm for building frontend assets
- `pdo_sqlite` enabled in PHP CLI to run the isolated PHPUnit suite

## Local setup (Windows / Laragon)

From PowerShell:

```powershell
cd "D:\laragon\www\Vesperia Test - Easy\vesperia-form"
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

Create a MySQL database, set the `DB_*` values in `.env`, and generate an application key if `APP_KEY` is empty:

```powershell
if (Select-String -Path .env -Pattern '^APP_KEY=$' -Quiet) { php artisan key:generate }
npm ci
npm run build
php artisan migrate --seed
php artisan db:seed --class=DemoUsersSeeder
php artisan db:seed --class=DemoReportsSeeder
php artisan serve
```

The JSON importer reads `submission.json` from the parent directory of this Laravel project. Open <http://127.0.0.1:8000>.

Demo credentials are for local/testing only:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@vesperia.test` | `AdminDemo!2026` |
| User | `user@vesperia.test` | `UserDemo!2026` |

## Roles

- New registrations are always assigned the `user` role; role input is not accepted from registration.
- Users can submit reports and view only their own reports.
- Admins can monitor and view reports but cannot submit, modify, or delete them.
- Promote a trusted account to admin from Tinker: `$user->forceFill(['role' => 'admin'])->save();`.

## API

All API endpoints return JSON.

- `POST /api/login` — JSON `email` and `password`; returns a Sanctum bearer token.
- `GET /api/forms` — paginated form summaries.
- `GET /api/forms/{slug}` — form schema.
- `POST /api/forms/{slug}/submissions` — submit `{ "answers": { ... } }`; guest submission is allowed, optional bearer authentication associates the report with a user, and admins are forbidden from submitting.
- `GET /api/forms/{slug}/submissions` — authenticated list, scoped to the signed-in user's reports or all reports for admins.

For token-authenticated requests use `Authorization: Bearer <token>`. First-party web requests use Sanctum's stateful session and require CSRF protection as normal. If using a custom local hostname, add it to `SANCTUM_STATEFUL_DOMAINS`.

## Storage and memory

`forms.schema` and `submissions.answers` use database JSON columns and Eloquent array casts. The migrations add a unique index on form slug and indexes for report lookups by form/date and user/date. API list endpoints are limited to 20 records per response; form summaries select only the columns they need.

## Tests

```powershell
php -d extension='D:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\ext\php_pdo_sqlite.dll' vendor\bin\phpunit
```

The test database is isolated in-memory SQLite. See [`answers.md`](./answers.md) for the requested technical question responses.
