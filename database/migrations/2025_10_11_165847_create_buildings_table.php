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
        DB::statement(query: 'CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create(table: 'buildings', callback: function (Blueprint $table) {
            $table->id();
            $table->string(column: 'address', length: 255);
            $table->timestamps();
        });

        DB::statement(query: 'ALTER TABLE buildings ADD COLUMN geom geometry(Point, 4326) NOT NULL');
        DB::statement(query: "ALTER TABLE buildings ADD COLUMN lon double precision GENERATED ALWAYS AS (ST_X(geom)) STORED");
        DB::statement(query: "ALTER TABLE buildings ADD COLUMN lat double precision GENERATED ALWAYS AS (ST_Y(geom)) STORED");
        DB::statement(query: 'CREATE INDEX buildings_geom_gist ON buildings USING GIST (geom)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'buildings');
    }
};
