<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create table attendance (
              id uuid primary key default gen_random_uuid(),
              student_id uuid not null references students(id) on delete cascade,
              org_code text not null references organizations(code),
              session_date date not null,
              status text not null check (status in ('present','absent')),
              created_at timestamptz not null default now(),
              updated_at timestamptz not null default now(),
              created_by uuid references admins(id),
              unique (student_id, session_date)
            );

            create index on attendance (org_code, session_date);
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop table if exists attendance;');
    }
};
