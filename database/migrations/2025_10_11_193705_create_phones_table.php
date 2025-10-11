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
        Schema::create(table: 'phones', callback: function (Blueprint $table) {
            $table->id();
            $table->string(column: 'phone', length: 16);
            $table->foreignId(column: 'organization_id')->constrained()->cascadeOnDelete();
            $table->unique(columns:['organization_id','phone']);
            $table->timestamps();
        });

        DB::statement(query: "ALTER TABLE phones ADD CONSTRAINT phones_phone_chk CHECK (phone ~ '^[1-9]-[0-9]{3}-[0-9]{3}(-[0-9]{2}-[0-9]{2})?$')");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'phones');
    }
};
