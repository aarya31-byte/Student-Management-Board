<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            -- Admins: the backend's own auth table (bcrypt hashes, JWT issued on login).
            -- org_code = null means access to both orgs (today's single shared admin);
            -- scoping a future staff member to one org only needs a differently-scoped row.
            create table admins (
              id uuid primary key default gen_random_uuid(),
              username text not null unique,
              password_hash text not null,
              full_name text not null,
              role text not null default 'admin' check (role in ('super_admin','admin')),
              org_code text references organizations(code),
              created_at timestamptz not null default now()
            );
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop table if exists admins;');
    }
};
