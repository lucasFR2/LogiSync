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
        Schema::create('incoming_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('access_key', 44)->unique();
            $table->string('number', 20);
            $table->string('series', 10);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_cnpj', 20)->nullable();
            $table->date('emission_date');
            $table->decimal('total_amount', 10, 2);
            $table->enum('manifestation_status', ['pending', 'ciencia', 'confirmada', 'desconhecimento', 'nao_realizada'])->default('pending');
            $table->enum('entry_status', ['pending', 'imported'])->default('pending');
            $table->longText('xml_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_invoices');
    }
};
