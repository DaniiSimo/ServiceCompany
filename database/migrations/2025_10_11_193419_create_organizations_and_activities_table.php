<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations_and_activities', function (Blueprint $table) {
            $table->foreignId(column: 'organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId(column: 'activity_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(columns:['organization_id','activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'organizations_and_activities');
    }
};
