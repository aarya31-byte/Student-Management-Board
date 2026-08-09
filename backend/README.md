# Student Management Board — Backend API

Laravel API backend for Ganishka Enterprises' Student Management Board (GT +
GA). Implements the contract in [`../backend_details.md`](../backend_details.md)
against Supabase-hosted Postgres. See that document for business rules,
schema rationale, and the full endpoint list.

## Architecture

Standard layered Laravel structure:

- `routes/api.php` — all 28 endpoints, grouped by auth + org-access middleware.
- `app/Http/Controllers/Api/*` — one controller per resource group (thin —
  request handling and response shaping only).
- `app/Http/Requests/*` — form request validation (one per write endpoint).
- `app/Http/Middleware/Authenticate.php` — verifies the JWT, loads the admin.
- `app/Http/Middleware/EnsureOrgAccess.php` — the org-scoping check from
  backend_details.md §5 (the *only* access control in the system).
- `app/Services/JwtService.php` — issues/verifies JWTs (firebase/php-jwt).
- `app/Models/*` — Eloquent models mapped onto the existing schema.
- `app/Support/MarksGuard.php` — shared "obtained ≤ total" cross-field check
  used by the three marks/problems endpoints.
- `database/migrations/*` — raw SQL lifted from backend_details.md §4,
  applied in dependency order. Each runs against the session-mode pooler
  (`pgsql_migrations` connection) since DDL needs full session semantics;
  runtime queries use a separate `pgsql` connection (transaction-mode pooler)
  with `PDO::ATTR_EMULATE_PREPARES` forced on — see `config/database.php`.
- `database/seeders/*` — first admin login + courses/subjects lookup data.

Percentage/grade/status are **never** computed in PHP — they're always read
from the Postgres views (`gt_assessment_results`, `gt_student_result_summary`,
`ga_final_exam_results`, `ga_student_result_summary`) or the
`grade_for_percentage()` SQL function, so there is exactly one place that
logic lives (backend_details.md §3).

## Prerequisites

- PHP 8.2+ with the `pdo_pgsql` extension, and Composer. The simplest way to
  get both on Windows is [Laravel Herd](https://herd.laravel.com/windows).
- The real Supabase database password (Project Settings → Database in the
  Supabase dashboard for project `yphafnnptmcwevdocirc`).

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

- Fill in `DB_PASSWORD` and replace `[YOUR-PASSWORD]` in both `DATABASE_URL`
  and `DIRECT_URL` with the real Supabase database password.
- Set `JWT_SECRET` to a freshly generated random value:
  `php -r "echo bin2hex(random_bytes(32));"`
- Set `ADMIN_SEED_PASSWORD` to a strong password for the first admin login
  (change it after first login — the seeder never re-reads it once the
  `admins` table already has a matching username).

Then apply the schema and seed lookup/admin data:

```bash
php artisan migrate
php artisan db:seed
```

Run the dev server:

```bash
php artisan serve --host=localhost --port=8000
```

The frontend's `API_BASE_URL` (in `17_script.js` and `03_login.js`) should
point at this address.

## Notes

- All error responses are normalized to `{"detail": ...}` (see
  `bootstrap/app.php`'s exception handlers) to match what the frontend
  already reads (backend_details.md §7).
- CORS is wide open (`CORS_ALLOWED_ORIGINS=*`) by default since the frontend
  has no fixed origin yet. Tighten `config/cors.php` / the env var once it's
  deployed somewhere specific.
- Running tests: `php artisan test` (phpunit.xml is configured; no test
  suite has been written yet beyond the base `Tests\TestCase`).
