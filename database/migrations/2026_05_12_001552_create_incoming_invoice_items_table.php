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
        Schema::create('incoming_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->string('ncm', 20)->nullable();
            $table->string('cfop', 10)->nullable();
            $table->string('unit', 10)->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 12, 4);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_invoice_items');
    }
};
