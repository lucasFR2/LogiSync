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
            $table->decimal('purchase_price', 12, 2)->default(0)->after('selling_price');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('purchase_price');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('tax_percent');
            $table->decimal('margin_percent', 5, 2)->default(0)->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'tax_percent', 'shipping_cost', 'margin_percent']);
        });
    }
};
