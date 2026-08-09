<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create or replace function gt_dashboard_stats()
            returns table (total_students bigint, total_assessments bigint, total_attendance bigint)
            language sql stable as $$
              select
                (select count(*) from students where org_code = 'gt'),
                (select count(*) from gt_assessments),
                (select count(*) from attendance where org_code = 'gt');
            $$;

            create or replace function ga_dashboard_stats()
            returns table (
              total_students bigint,
              total_coding_records bigint,
              total_exam_records bigint,
              passed_students bigint,
              needs_improvement_students bigint,
              not_attempted_students bigint
            )
            language sql stable as $$
              select
                (select count(*) from students where org_code = 'ga'),
                (select count(*) from ga_coding_practice),
                (select count(*) from ga_final_exam),
                (select count(distinct student_id) from ga_student_result_summary where status = 'Passed'),
                (select count(distinct student_id) from ga_student_result_summary where status = 'Needs Improvement'),
                (select count(distinct student_id) from ga_student_result_summary where status = 'Not Attempted');
            $$;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop function if exists ga_dashboard_stats();
            drop function if exists gt_dashboard_stats();
        SQL);
    }
};
