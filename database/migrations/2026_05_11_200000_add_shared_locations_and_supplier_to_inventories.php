<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->boolean('allow_shared')->default(false)->after('is_occupied');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('user_id')
                  ->constrained('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn('allow_shared');
        });
    }
};
