<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create or replace function set_updated_at()
            returns trigger language plpgsql as $$
            begin
              new.updated_at = now();
              return new;
            end;
            $$;

            create trigger trg_students_updated_at before update on students
              for each row execute function set_updated_at();
            create trigger trg_attendance_updated_at before update on attendance
              for each row execute function set_updated_at();
            create trigger trg_gt_assessments_updated_at before update on gt_assessments
              for each row execute function set_updated_at();
            create trigger trg_ga_coding_practice_updated_at before update on ga_coding_practice
              for each row execute function set_updated_at();
            create trigger trg_ga_final_exam_updated_at before update on ga_final_exam
              for each row execute function set_updated_at();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop trigger if exists trg_ga_final_exam_updated_at on ga_final_exam;
            drop trigger if exists trg_ga_coding_practice_updated_at on ga_coding_practice;
            drop trigger if exists trg_gt_assessments_updated_at on gt_assessments;
            drop trigger if exists trg_attendance_updated_at on attendance;
            drop trigger if exists trg_students_updated_at on students;
            drop function if exists set_updated_at();
        SQL);
    }
};
