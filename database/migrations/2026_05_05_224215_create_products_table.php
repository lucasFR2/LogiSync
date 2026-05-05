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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->text('description')->nullable();
            
            // Prices and Stock
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('max_stock')->nullable();
            $table->integer('reorder_level');
            $table->decimal('package_quantity', 10, 2)->default(1);
            
            // Dimensions and Weight
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('depth', 10, 2)->nullable();
            
            // Categorization and Location
            $table->string('category')->nullable();
            $table->string('unit')->default('un');
            $table->string('warehouse_location')->nullable();
            $table->string('supplier')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'descontinuado'])->default('ativo');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};