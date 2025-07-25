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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('turn_format', ['random', 'linear'])->default('linear');
            $table->enum('privacy_type', ['public', 'private'])->default('public');
            $table->boolean('requires_approval')->default(false);
            $table->text('description')->nullable();
            $table->decimal('contribution_amount', 10, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->integer('max_members');
            $table->integer('current_members')->default(0);
            $table->date('start_date');
            $table->enum('status', ['open', 'active', 'completed', 'cancelled'])->default('open');

            // Fix: Use uuid foreign key for created_by
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('groups');
    }
};
