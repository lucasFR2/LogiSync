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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->string('aisle', 10);      // Rua / Corredor
            $table->string('column', 10);     // Coluna / Posição horizontal
            $table->string('level', 10);      // Nível (1-7)
            $table->string('full_code')->unique(); // Ex: R01-C05-L1
            $table->boolean('is_occupied')->default(false);
            $table->timestamps();
            
            $table->index(['aisle', 'column', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
