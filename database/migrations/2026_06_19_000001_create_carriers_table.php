<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('name')->comment('Razão Social / Nome');
            $table->string('cnpj')->nullable()->unique()->comment('CNPJ');
            $table->string('state_registration')->nullable()->comment('Inscrição Estadual');
            $table->string('contact')->nullable()->comment('Pessoa de contato');

            // Contato
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Dados de transporte
            $table->string('antt')->nullable()->comment('Registro ANTT / RNTRC');
            $table->string('vehicle_plate')->nullable()->comment('Placa do veículo padrão');
            $table->string('vehicle_uf', 2)->nullable()->comment('UF da placa');
            $table->string('vehicle_type')->nullable()->comment('Tipo de veículo (caminhão, van, etc.)');

            // Endereço
            $table->string('street')->nullable()->comment('Logradouro');
            $table->string('number')->nullable()->comment('Número');
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable()->comment('Bairro');
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('cnpj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
