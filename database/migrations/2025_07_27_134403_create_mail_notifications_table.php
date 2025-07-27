<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid');
            $table->string('mail_type'); // welcome, group_join, group_leave, contribution_start
            $table->string('subject');
            $table->text('message_content');
            $table->json('mail_data')->nullable(); // Additional data for the mail
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->string('language', 10)->default('en');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_notifications');
    }
};