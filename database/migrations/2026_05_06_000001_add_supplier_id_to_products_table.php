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
            // Remover coluna supplier (texto)
            if (Schema::hasColumn('products', 'supplier')) {
                $table->dropColumn('supplier');
            }

            // Adicionar coluna supplier_id (chave estrangeira)
            if (! Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->constrained('suppliers')
                    ->onDelete('set null')
                    ->after('warehouse_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remover coluna supplier_id
            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropForeignKeyIfExists(['supplier_id']);
                $table->dropColumn('supplier_id');
            }

            // Restaurar coluna supplier (texto)
            if (! Schema::hasColumn('products', 'supplier')) {
                $table->string('supplier')->nullable();
            }
        });
    }
};
