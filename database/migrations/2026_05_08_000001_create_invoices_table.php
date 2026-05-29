<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // NF-e número ex: NF-2026-00001
            $table->string('series')->default('001');
            $table->enum('type', ['entrada', 'saida'])->default('saida');
            $table->enum('status', ['rascunho', 'emitida', 'cancelada'])->default('rascunho');

            // Emitente (empresa)
            $table->string('issuer_name')->default('LogiSync Distribuidora Ltda');
            $table->string('issuer_cnpj')->default('00.000.000/0001-00');
            $table->string('issuer_address')->default('Rua das Mercadorias, 100 - Centro');
            $table->string('issuer_city')->default('São Paulo');
            $table->string('issuer_state')->default('SP');
            $table->string('issuer_zip')->default('01000-000');

            // Destinatário
            $table->string('recipient_name');
            $table->string('recipient_document')->nullable(); // CPF ou CNPJ
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_address')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_state')->nullable();
            $table->string('recipient_zip')->nullable();

            // Financeiro
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Informações adicionais
            $table->enum('payment_method', ['dinheiro', 'pix', 'boleto', 'cartao_credito', 'cartao_debito', 'transferencia'])->default('pix');
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->date('issued_at')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->string('unit')->default('un');
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0); // % de desconto
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
