<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(query: 'CREATE EXTENSION IF NOT EXISTS ltree');

        Schema::create(table: 'activities', callback: function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name',length: 255);
            $table->timestamps();
        });

        DB::statement(query:'ALTER TABLE activities ADD COLUMN path ltree NOT NULL');
        DB::statement(query:'ALTER TABLE activities ADD COLUMN level smallint GENERATED ALWAYS AS (nlevel(path)) STORED');
        DB::statement(query:'ALTER TABLE activities ADD CONSTRAINT activities_level_chk CHECK (level BETWEEN 1 AND 3)');

        DB::statement(query:'CREATE INDEX activities_path_gist ON activities USING GIST (path)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table:  'activities');
        DB::statement(query: 'DROP EXTENSION IF EXISTS ltree');
    }
};
