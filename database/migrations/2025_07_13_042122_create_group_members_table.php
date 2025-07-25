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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('group_uuid');
            $table->uuid('user_uuid');
            $table->foreign('group_uuid')->references('uuid')->on('groups')->onDelete('cascade');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');

            $table->timestamp('joined_at');
            $table->integer('payout_position')->nullable();
            $table->integer('is_rolled')->default(0);
            $table->timestamps();
            $table->unique(['group_uuid', 'user_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
