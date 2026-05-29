<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->string('phone')->nullable();
            $blueprint->string('zip_code')->nullable();
            $blueprint->string('address')->nullable();
            $blueprint->string('number')->nullable();
            $blueprint->string('neighborhood')->nullable();
            $blueprint->string('city')->nullable();
            $blueprint->string('state', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['phone', 'zip_code', 'address', 'number', 'neighborhood', 'city', 'state']);
        });
    }
};
