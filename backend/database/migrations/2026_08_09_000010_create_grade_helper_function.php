<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            -- Single source of truth for the grade bands in backend_details.md §3.
            create or replace function grade_for_percentage(pct numeric)
            returns text language sql immutable as $$
              select case
                when pct >= 90 then 'A+'
                when pct >= 80 then 'A'
                when pct >= 70 then 'B+'
                when pct >= 60 then 'B'
                when pct >= 50 then 'C'
                when pct >= 40 then 'D'
                else 'F'
              end;
            $$;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop function if exists grade_for_percentage(numeric);');
    }
};
