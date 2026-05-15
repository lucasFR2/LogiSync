<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Inventory;
use App\Models\WarehouseLocation;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

echo "Starting data generation...\n";

$categories = ['Geral', 'Móveis', 'Eletrônicos', 'Alimentos'];

for ($i = 1; $i <= 10; $i++) {
    $cat = $categories[array_rand($categories)];
    $p = Product::create([
        'name' => "Produto Exemplo $i",
        'sku' => "SKU-" . str_pad($i + 10, 5, '0', STR_PAD_LEFT),
        'quantity' => rand(50, 200),
        'reorder_level' => 30,
        'category' => $cat,
        'unit_price' => rand(10, 500),
        'warehouse_location_id' => rand(1, 14000),
        'status' => 'ativo'
    ]);
    
    Inventory::create([
        'product_id' => $p->id,
        'quantity' => $p->quantity,
        'type' => 'entrada',
        'status' => 'confirmada',
        'user_id' => 1,
        'notes' => 'Carga inicial do sistema'
    ]);
    
    if ($p->warehouse_location_id) {
        $loc = WarehouseLocation::find($p->warehouse_location_id);
        if ($loc) {
            $loc->is_occupied = true;
            $loc->save();
        }
    }

    ActivityLog::create([
        'user_id' => 1,
        'action' => 'entrada_estoque',
        'description' => "Entrada do produto " . $p->name,
        'ip_address' => '127.0.0.1'
    ]);
}

echo "Done! Created 10 more products with logs.\n";
