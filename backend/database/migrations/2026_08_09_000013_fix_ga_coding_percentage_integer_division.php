<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    // Fixes a bug in the original view definition (migration 000011):
    // solved_problems/total_problems are integer columns, so dividing them
    // directly triggers Postgres integer division (e.g. 15/20 -> 0 instead
    // of 0.75), making coding_percentage always come out wrong. Migration
    // 000011 already has the corrected SQL for fresh installs; this
    // forward-only fix re-creates the view for environments where the buggy
    // version was already applied, without touching any data.
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create or replace view ga_student_result_summary as
            select
              s.id as student_id,
              coalesce(sum(c.solved_problems), 0) as coding_solved,
              coalesce(sum(c.total_problems), 0) as coding_total,
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
        // Intentionally a no-op — reverting would restore the integer
        // division bug. Roll back past migration 000011 instead if this
        // view needs to be fully removed.
    }
};
