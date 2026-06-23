<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('icms_red_bc', 5, 2)->default(0)->after('icms_orig');
            $table->integer('icms_mod_bc')->default(3)->after('icms_red_bc');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['icms_red_bc', 'icms_mod_bc']);
        });
    }
};
