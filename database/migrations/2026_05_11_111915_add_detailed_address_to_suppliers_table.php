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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('street')->nullable()->after('state_registration');
            $table->string('number', 20)->nullable()->after('street');
            $table->string('neighborhood')->nullable()->after('number');
            $table->string('zip_code', 20)->nullable()->after('neighborhood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['street', 'number', 'neighborhood', 'zip_code']);
        });
    }
};
