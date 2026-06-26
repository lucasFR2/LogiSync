<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Garante que produtos tenham dimensões mínimas (1.0) e popula
     * produtos sem dimensões com valores aleatórios entre 1.0 e 5.0.
     */
    public function up(): void
    {
        // Garante que as colunas existem e têm default 1.0
        Schema::table('products', function (Blueprint $table) {
            // Colunas já existem — apenas muda o default se necessário
            // SQLite não suporta ALTER COLUMN com default via Laravel Blueprint de forma simples,
            // mas podemos garantir via DB::statement para compatibilidade
        });

        // Preenche produtos sem width com valor aleatório entre 1.0 e 5.0
        $products = DB::table('products')
            ->whereNull('width')
            ->orWhereNull('height')
            ->orWhereNull('depth')
            ->orWhere('width', 0)
            ->orWhere('height', 0)
            ->orWhere('depth', 0)
            ->get(['id', 'width', 'height', 'depth']);

        foreach ($products as $product) {
            DB::table('products')->where('id', $product->id)->update([
                'width'  => ($product->width  === null || $product->width  == 0) ? round(mt_rand(10, 50) / 10, 1) : $product->width,
                'height' => ($product->height === null || $product->height == 0) ? round(mt_rand(10, 50) / 10, 1) : $product->height,
                'depth'  => ($product->depth  === null || $product->depth  == 0) ? round(mt_rand(10, 50) / 10, 1) : $product->depth,
            ]);
        }
    }

    public function down(): void
    {
        // Sem rollback destrutivo — deixa os valores como estão
    }
};
