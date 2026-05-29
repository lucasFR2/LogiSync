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
        // Adding indexes one by one to avoid stopping on duplicate errors
        $this->addIndexSafe('products', ['name', 'category', 'status']);
        $this->addIndexSafe('customers', ['name']);
        $this->addIndexSafe('suppliers', ['name', 'cnpj']);
        $this->addIndexSafe('inventories', ['type', 'created_at']);
        $this->addIndexSafe('activity_logs', ['action', 'created_at']);
        $this->addIndexSafe('warehouse_locations', ['full_code', 'aisle', 'is_occupied']);
    }

    private function addIndexSafe(string $table, array $columns): void
    {
        foreach ($columns as $column) {
            try {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                    $tableBlueprint->index($column);
                });
            } catch (\Exception $e) {
                // Silently skip if index already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafe('products', ['name', 'category', 'status']);
        $this->dropIndexSafe('customers', ['name']);
        $this->dropIndexSafe('suppliers', ['name', 'cnpj']);
        $this->dropIndexSafe('inventories', ['type', 'created_at']);
        $this->dropIndexSafe('activity_logs', ['action', 'created_at']);
        $this->dropIndexSafe('warehouse_locations', ['full_code', 'aisle', 'is_occupied']);
    }

    private function dropIndexSafe(string $table, array $columns): void
    {
        foreach ($columns as $column) {
            try {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                    $tableBlueprint->dropIndex([$column]);
                });
            } catch (\Exception $e) {
                // Silently skip if index doesn't exist
            }
        }
    }
};
