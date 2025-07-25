<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum values
        DB::statement("UPDATE groups SET turn_format = 'linear' WHERE turn_format NOT IN ('random', 'linear', 'manual')");
        
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('turn_format', ['random', 'linear', 'manual'])->default('linear')->change();
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('turn_format', ['random', 'linear'])->default('linear')->change();
        });
    }
};