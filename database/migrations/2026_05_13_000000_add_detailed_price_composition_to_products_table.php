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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('ipi_percent', 8, 4)->default(0)->after('tax_percent');
            $table->decimal('icms_st_percent', 8, 4)->default(0)->after('ipi_percent');
            $table->decimal('other_taxes_percent', 8, 4)->default(0)->after('icms_st_percent');
            $table->decimal('other_costs', 12, 2)->default(0)->after('shipping_cost');
            $table->decimal('discount_percent', 8, 4)->default(0)->after('margin_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ipi_percent', 'icms_st_percent', 'other_taxes_percent', 'other_costs', 'discount_percent']);
        });
    }
};
