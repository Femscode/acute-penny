<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->uuid('current_turn_user_uuid')->nullable()->after('status');
            $table->integer('current_cycle')->default(1)->after('current_turn_user_uuid');
            $table->date('contribution_started_at')->nullable()->after('current_cycle');
            $table->foreign('current_turn_user_uuid')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['current_turn_user_uuid']);
            $table->dropColumn(['current_turn_user_uuid', 'current_cycle', 'contribution_started_at']);
        });
    }
};