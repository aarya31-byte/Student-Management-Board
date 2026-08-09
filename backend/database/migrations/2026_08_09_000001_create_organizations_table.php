<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // DDL needs full session semantics — always run migrations against the
    // session-mode pooler (port 5432), never the transaction-mode pooler
    // used at runtime. See backend_details.md §2.
    protected $connection = 'pgsql_migrations';

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            create extension if not exists pgcrypto;

            create table organizations (
              code text primary key check (code in ('gt','ga')),
              name text not null
            );

            insert into organizations (code, name) values
              ('gt', 'Ganishka Technology'),
              ('ga', 'Ganishka Academy');
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('drop table if exists organizations;');
    }
};
