<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [];
        $aisles = range(1, 40);
        $columns = range(1, 50);
        $levels = range(1, 7);

        $now = now();
        $count = 0;

        foreach ($aisles as $aisle) {
            $aisleCode = 'R' . str_pad($aisle, 2, '0', STR_PAD_LEFT);
            foreach ($columns as $column) {
                $columnCode = 'C' . str_pad($column, 2, '0', STR_PAD_LEFT);
                foreach ($levels as $level) {
                    $levelCode = 'L' . $level;
                    $fullCode = "$aisleCode-$columnCode-$levelCode";

                    $locations[] = [
                        'aisle' => $aisleCode,
                        'column' => $columnCode,
                        'level' => $levelCode,
                        'full_code' => $fullCode,
                        'is_occupied' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($locations) >= 1000) {
                        \DB::table('warehouse_locations')->insert($locations);
                        $locations = [];
                    }
                    $count++;
                }
            }
        }

        if (count($locations) > 0) {
            \DB::table('warehouse_locations')->insert($locations);
        }
        
        $this->command->info("Geradas $count posições de estoque com sucesso!");
    }
}
