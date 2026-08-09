<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            -- Computed result views: percentage/grade/status are always derived on
            -- read, never stored, so editing a mark later can't leave a stale
            -- percentage/grade behind (backend_details.md §3).
            create view gt_assessment_results as
            select
              a.*,
              round(a.obtained_marks / a.total_marks * 100, 2) as percentage,
              grade_for_percentage(round(a.obtained_marks / a.total_marks * 100, 2)) as grade
            from gt_assessments a;

            create view gt_student_result_summary as
            select
              s.id as student_id,
              s.org_code,
              coalesce(sum(a.total_marks), 0) as total_marks,
              coalesce(sum(a.obtained_marks), 0) as obtained_marks,
              round(sum(a.obtained_marks) / nullif(sum(a.total_marks), 0) * 100, 2) as percentage,
              case
                when count(a.id) = 0 then 'No Result Available'
                when round(sum(a.obtained_marks) / nullif(sum(a.total_marks), 0) * 100, 2) >= 75 then 'Excellent'
                when round(sum(a.obtained_marks) / nullif(sum(a.total_marks), 0) * 100, 2) >= 60 then 'Very Good'
                when round(sum(a.obtained_marks) / nullif(sum(a.total_marks), 0) * 100, 2) >= 50 then 'Good'
                when round(sum(a.obtained_marks) / nullif(sum(a.total_marks), 0) * 100, 2) >= 35 then 'Needs Improvement'
                else 'Requires More Practice'
              end as overall_remark
            from students s
            left join gt_assessments a on a.student_id = s.id
            where s.org_code = 'gt'
            group by s.id, s.org_code;

            create view ga_final_exam_results as
            select
              e.*,
              round(e.obtained_marks / e.total_marks * 100, 2) as percentage,
              grade_for_percentage(round(e.obtained_marks / e.total_marks * 100, 2)) as grade,
              case when round(e.obtained_marks / e.total_marks * 100, 2) >= 40 then 'Passed' else 'Needs Improvement' end as status
            from ga_final_exam e;

            create view ga_student_result_summary as
            select
              s.id as student_id,
              coalesce(sum(c.solved_problems), 0) as coding_solved,
              coalesce(sum(c.total_problems), 0) as coding_total,
              -- solved_problems/total_problems are integer columns; casting
              -- to numeric before dividing avoids Postgres integer division
              -- silently truncating (e.g. 15/20 -> 0 instead of 0.75).
              round(sum(c.solved_problems)::numeric / nullif(sum(c.total_problems), 0) * 100, 2) as coding_percentage,
              coalesce(sum(e.obtained_marks), 0) as exam_obtained,
              coalesce(sum(e.total_marks), 0) as exam_total,
              round(sum(e.obtained_marks) / nullif(sum(e.total_marks), 0) * 100, 2) as exam_percentage,
              grade_for_percentage(round(sum(e.obtained_marks) / nullif(sum(e.total_marks), 0) * 100, 2)) as exam_grade,
              case
                when count(e.id) = 0 then 'Not Attempted'
                when round(sum(e.obtained_marks) / nullif(sum(e.total_marks), 0) * 100, 2) >= 40 then 'Passed'
                else 'Needs Improvement'
              end as status
            from students s
            left join ga_coding_practice c on c.student_id = s.id
            left join ga_final_exam e on e.student_id = s.id
            where s.org_code = 'ga'
            group by s.id;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop view if exists ga_student_result_summary;
            drop view if exists ga_final_exam_results;
            drop view if exists gt_student_result_summary;
            drop view if exists gt_assessment_results;
        SQL);
    }
};
