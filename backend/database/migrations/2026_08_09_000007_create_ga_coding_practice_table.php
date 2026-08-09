<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create table ga_coding_practice (
              id uuid primary key default gen_random_uuid(),
              student_id uuid not null references students(id) on delete cascade,
              topic_id bigint references subjects(id),
              total_problems integer not null check (total_problems > 0),
              solved_problems integer not null check (solved_problems >= 0 and solved_problems <= total_problems),
              created_at timestamptz not null default now(),
              updated_at timestamptz not null default now(),
              created_by uuid references admins(id)
            );

            create index on ga_coding_practice (student_id);
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop table if exists ga_coding_practice;');
    }
};
