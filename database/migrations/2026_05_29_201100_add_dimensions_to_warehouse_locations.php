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
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->decimal('width', 8, 2)->nullable()->after('full_code');
            $table->decimal('height', 8, 2)->nullable()->after('width');
            $table->decimal('depth', 8, 2)->nullable()->after('height');
            $table->decimal('max_weight', 10, 2)->nullable()->after('depth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'depth', 'max_weight']);
        });
    }
};
