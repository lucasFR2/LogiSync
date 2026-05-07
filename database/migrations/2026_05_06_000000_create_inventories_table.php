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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade')
                ->comment('ID do produto');

            // Entry Information
            $table->integer('quantity')->comment('Quantidade movimentada');
            $table->enum('type', [
                'entrada',
                'saida',
                'devolucao',
                'ajuste',
                'transferencia',
            ])->default('entrada')->comment('Tipo de movimentação');

            // Additional Details
            $table->string('reference')->nullable()->comment('Referência: NF, Pedido, etc');
            $table->text('notes')->nullable()->comment('Observações sobre a entrada');
            $table->enum('status', [
                'pendente',
                'confirmada',
                'cancelada',
            ])->default('confirmada')->comment('Status da movimentação');

            // Audit
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Usuário que registrou a entrada');

            $table->date('entry_date')->nullable()->comment('Data da entrada (pode diferir da data do registro)');
            $table->string('lot_number')->nullable()->comment('Número do lote/série');
            $table->date('expiry_date')->nullable()->comment('Data de validade (quando aplicável)');

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('type');
            $table->index('status');
            $table->index('entry_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
