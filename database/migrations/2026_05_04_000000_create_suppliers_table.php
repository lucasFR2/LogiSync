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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Nome do fornecedor');
            $table->string('contact')->nullable()->comment('Pessoa de contato');
            $table->string('phone')->nullable()->comment('Telefone');
            $table->string('address')->nullable()->comment('Endereço');
            $table->string('email')->nullable()->comment('Email');
            $table->string('city')->nullable()->comment('Cidade');
            $table->string('state', 2)->nullable()->comment('Estado (UF)');
            $table->string('cnpj')->nullable()->unique()->comment('CNPJ');

            $table->timestamps();

            // Índices
            $table->index('name');
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
