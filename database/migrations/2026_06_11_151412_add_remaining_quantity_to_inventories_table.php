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
        Schema::table('inventories', function (Blueprint $table) {
            $table->integer('remaining_quantity')->default(0)->after('quantity')->comment('Quantidade restante do lote (para FIFO)');
        });

        // Initialize remaining_quantity for entries
        DB::table('inventories')
            ->where('type', 'entrada')
            ->where('status', 'confirmada')
            ->update(['remaining_quantity' => DB::raw('quantity')]);

        // Reconcile existing data
        $productIds = DB::table('inventories')->distinct()->pluck('product_id');

        foreach ($productIds as $productId) {
            $exits = DB::table('inventories')
                ->where('product_id', $productId)
                ->where('type', 'saida')
                ->where('status', 'confirmada')
                ->orderBy('entry_date')
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

            foreach ($exits as $exit) {
                $qtyToDeduct = $exit->quantity;

                $entries = DB::table('inventories')
                    ->where('product_id', $productId)
                    ->where('type', 'entrada')
                    ->where('status', 'confirmada')
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('entry_date')
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get();

                foreach ($entries as $entry) {
                    if ($qtyToDeduct <= 0) {
                        break;
                    }

                    if ($entry->remaining_quantity >= $qtyToDeduct) {
                        DB::table('inventories')
                            ->where('id', $entry->id)
                            ->update(['remaining_quantity' => $entry->remaining_quantity - $qtyToDeduct]);
                        $qtyToDeduct = 0;
                    } else {
                        DB::table('inventories')
                            ->where('id', $entry->id)
                            ->update(['remaining_quantity' => 0]);
                        $qtyToDeduct -= $entry->remaining_quantity;
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('remaining_quantity');
        });
    }
};
