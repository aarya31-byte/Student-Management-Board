<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            -- Lookup tables, replacing hardcoded <select> option lists that had drifted
            -- out of sync between pages in the old frontend.
            create table courses (
              id bigint generated always as identity primary key,
              org_code text not null references organizations(code),
              name text not null,
              unique (org_code, name)
            );

            create table subjects (
              id bigint generated always as identity primary key,
              org_code text not null references organizations(code),
              kind text not null check (kind in ('gt_subject','ga_exam_subject','ga_coding_topic')),
              name text not null,
              unique (org_code, kind, name)
            );
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop table if exists subjects;
            drop table if exists courses;
        SQL);
    }
};
