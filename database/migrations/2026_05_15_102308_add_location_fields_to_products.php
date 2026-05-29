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
            // Campos de localização física no estoque
            $table->string('warehouse_location_code')->nullable()->comment('Código da localização (ex: A-01-03)');
            $table->string('aisle')->nullable()->comment('Corredor/Seção (ex: A, B, C)');
            $table->string('shelf')->nullable()->comment('Prateleira (ex: 01, 02)');
            $table->string('level')->nullable()->comment('Andar/Nível (ex: 1, 2, 3)');
            $table->string('box')->nullable()->comment('Box/Bin (ex: 1, 2, 3)');
            $table->text('location_notes')->nullable()->comment('Observações adicionais sobre a localização');
            $table->dateTime('location_updated_at')->nullable()->comment('Última atualização da localização');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'warehouse_location_code',
                'aisle',
                'shelf',
                'level',
                'box',
                'location_notes',
                'location_updated_at'
            ]);
        });
    }
};
