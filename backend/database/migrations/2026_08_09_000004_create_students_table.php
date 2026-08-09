<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create table students (
              id uuid primary key default gen_random_uuid(),
              org_code text not null references organizations(code),
              display_id text not null,          -- e.g. GT001 / GA001
              name text not null,
              batch text not null,
              course_id bigint references courses(id),
              duration text not null,
              created_at timestamptz not null default now(),
              updated_at timestamptz not null default now(),
              created_by uuid references admins(id),
              unique (org_code, display_id)
            );

            create index on students (org_code);

            -- Sequence-backed display_id generator (collision-safe, unlike the old frontend
            -- which derived IDs from array length and could collide after a delete).
            create sequence gt_student_seq;
            create sequence ga_student_seq;

            create or replace function set_student_display_id()
            returns trigger language plpgsql as $$
            begin
              if new.display_id is null then
                if new.org_code = 'gt' then
                  new.display_id := 'GT' || lpad(nextval('gt_student_seq')::text, 3, '0');
                else
                  new.display_id := 'GA' || lpad(nextval('ga_student_seq')::text, 3, '0');
                end if;
              end if;
              return new;
            end;
            $$;

            create trigger trg_student_display_id
              before insert on students
              for each row execute function set_student_display_id();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop trigger if exists trg_student_display_id on students;
            drop function if exists set_student_display_id();
            drop sequence if exists gt_student_seq;
            drop sequence if exists ga_student_seq;
            drop table if exists students;
        SQL);
    }
};
