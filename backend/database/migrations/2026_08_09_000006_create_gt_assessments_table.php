<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            -- Merges the old gtAssignments + gtMarks + the never-shipped Projects/Work
            -- concept into one entity (see backend_details.md §8 for why).
            create table gt_assessments (
              id uuid primary key default gen_random_uuid(),
              student_id uuid not null references students(id) on delete cascade,
              type text not null default 'assignment' check (type in ('assignment','project')),
              name text not null,
              subject_id bigint references subjects(id),
              total_marks numeric not null check (total_marks > 0),
              obtained_marks numeric not null check (obtained_marks >= 0 and obtained_marks <= total_marks),
              remark text,
              created_at timestamptz not null default now(),
              updated_at timestamptz not null default now(),
              created_by uuid references admins(id)
            );

            create index on gt_assessments (student_id);
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop table if exists gt_assessments;');
    }
};
