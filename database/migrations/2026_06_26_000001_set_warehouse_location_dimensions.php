<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Define todos os estoques como cubos fixos 10×10×10 = 1.000 unidades³.
     * Garante que allow_shared esteja ativo em todas as posições existentes.
     */
    public function up(): void
    {
        // Garante que a coluna allow_shared existe (foi adicionada em migração anterior)
        if (!Schema::hasColumn('warehouse_locations', 'allow_shared')) {
            Schema::table('warehouse_locations', function (Blueprint $table) {
                $table->boolean('allow_shared')->default(true)->after('is_occupied');
            });
        }

        // Define as dimensões 10×10×10 em todas as localizações existentes
        DB::table('warehouse_locations')->update([
            'width'  => 10.00,
            'height' => 10.00,
            'depth'  => 10.00,
        ]);
    }

    public function down(): void
    {
        // Não há rollback sensato — não podemos saber os valores anteriores
        // Apenas zera as dimensões
        DB::table('warehouse_locations')->update([
            'width'  => null,
            'height' => null,
            'depth'  => null,
        ]);
    }
};
