# Student Management Board — Ganishka Enterprises

Admin-only back-office tool for **Ganishka Enterprises**, which runs two independent
sub-organizations through the same login (picked from a chooser screen after
sign-in):

- **GT — Ganishka Technology**: coding/IT training (Students, Attendance,
  Assignments/Marks, Results).
- **GA — Ganishka Academy**: general academy (Students, Coding Practice,
  Attendance, Final Exam, Results).

There is no student-facing login and no public sign-up — one admin account
manages both verticals.

---

## 1. Project structure

```
Student-Management-Board/
├── backend/            Laravel 12 API (PHP 8.2+, Supabase Postgres)
├── *.html, *.js, *.css  Frontend: plain HTML/CSS/vanilla JS, no build step
```

The two halves are independent — the frontend is a static site that talks to
the backend purely over HTTP (JSON + a Bearer JWT). See
[`backend/README.md`](backend/README.md) for backend architecture details
(migrations, JWT flow, validation, etc.).

---

## 2. Tech stack

| Layer | Tech |
|---|---|
| Backend | PHP 8.2+, Laravel 12, JWT auth (`firebase/php-jwt`), Postgres (Supabase-hosted) |
| Frontend | Plain HTML/CSS/vanilla JS — no framework, no build step, no `npm install` |
| Database | Postgres via Supabase's connection pooler (see §6 for why there are two pooler ports) |

---

## 3. Prerequisites (any machine)

- **PHP 8.2 or newer**, with the `pdo_pgsql` extension enabled.
- **Composer** (PHP's dependency manager).
- A **Postgres database** to point the backend at — this project was built
  against Supabase, but any reachable Postgres instance works the same way.
- Any modern web browser, to open the frontend.

**Don't have PHP/Composer yet?**
- **Windows**: easiest path is [Laravel Herd](https://herd.laravel.com/windows) (bundles both). Alternative: `winget install PHP.PHP.8.4`, then install Composer manually from [getcomposer.org](https://getcomposer.org/download/).
- **Mac**: `brew install php composer`, or [Laravel Herd for Mac](https://herd.laravel.com).
- **Linux**: `sudo apt install php php-pgsql composer` (or your distro's equivalent).

Verify both are on your `PATH` before continuing:
```bash
php -v
composer --version
```

> **Windows-specific gotcha**: after installing PHP, a PowerShell/terminal
> window that was already open **will not** see it — Windows only refreshes
> `PATH` into new processes, and not always reliably even then. Always open
> a **fresh** terminal window after installing PHP/Composer.

---

## 4. Backend setup (do this first)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Now edit `backend/.env` — **this file is gitignored and must never be
committed.** Fill in:

| Variable | What it is |
|---|---|
| `DB_PASSWORD` | Your Postgres database password |
| `DATABASE_URL` / `DIRECT_URL` | Replace `[YOUR-PASSWORD]` in both with the same password (URL-encode special characters — e.g. `@` becomes `%40`) |
| `JWT_SECRET` | A random signing secret. Generate one with: `php -r "echo bin2hex(random_bytes(32));"` |
| `ADMIN_SEED_USERNAME` / `ADMIN_SEED_PASSWORD` | Credentials for the first admin login — pick your own; used only once, by the seeder below |

Then apply the schema and seed lookup/admin data:

```bash
php artisan migrate
php artisan db:seed
```

Start the server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Leave that terminal open — it's your running server. Verify it's alive by
opening `http://127.0.0.1:8000/up` in a browser (should show `OK`).

---

## 5. Frontend setup

No install step — it's static HTML, at the repo root (not inside `backend/`).
Two things to check:

1. **Point it at your backend.** Open both `17_script.js` and `03_login.js`
   and confirm `API_BASE_URL` matches where your backend is actually running
   (default: `http://localhost:8000` — matches step 4 above if you used the
   same host/port).
2. **Open it.** Double-click `01_index.html`, or serve it with any static
   file server if your browser is picky about `file://` pages making
   network requests:
   ```bash
   php -S localhost:5500
   # then open http://localhost:5500/01_index.html
   ```

Log in with the `ADMIN_SEED_USERNAME` / `ADMIN_SEED_PASSWORD` you set in
`backend/.env` and seeded in step 4.

---

## 6. Common setup problems

- **`php`/`composer` not recognized** — open a brand-new terminal window (see the Windows gotcha in §3). If it still fails, run `php -v` with the full install path to confirm PHP actually installed, then check it's really on `PATH` (`echo $PATH` / `$env:PATH`).
- **"prepared statement already exists" errors under load** — if you're on Supabase (or any pgbouncer-fronted Postgres) in *transaction pooling mode*, the driver must not use server-side prepared statements. This project's `backend/config/database.php` already sets `PDO::ATTR_EMULATE_PREPARES => true` for exactly this reason — don't remove it.
- **Migrations fail / DDL errors** — migrations need a connection with full session semantics (multi-statement transactions, DDL). If you're on Supabase, migrations must run through the **session-mode pooler (port 5432)**, not the transaction-mode pooler (port 6543) used for normal runtime queries. This project already splits these into two connections (`pgsql` vs `pgsql_migrations`) — just make sure both `DATABASE_URL` (6543) and `DIRECT_URL` (5432) are filled in correctly in `.env`.
- **CORS errors in the browser console** — the backend's `CORS_ALLOWED_ORIGINS` defaults to `*` (any origin), which should cover local development and `file://` pages. If you've tightened it for a real deployment, make sure it includes wherever the frontend is actually served from.
- **Login works but every other page 401s / bounces back to login** — check that `localStorage.authToken` is actually being set after login (browser dev tools → Application → Local Storage), and that `API_BASE_URL` in both JS files points at a reachable backend.

---

## 7. Security notes for anyone cloning this

- `backend/.env` is **never** committed (it's in `.gitignore`) — every clone
  needs its own, created fresh from `.env.example` per §4.
- The seeded admin password is meant to be changed after first login.
- Never hardcode the database password, JWT secret, or any admin credential
  anywhere in the codebase — they only ever live in the local `.env`.
