<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->json('virtual_account_data')->nullable()->after('payment_reference');
        });
    }

    public function down()
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'virtual_account_data']);
        });
    }
};