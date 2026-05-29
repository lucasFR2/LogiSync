<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\WarehouseLocation::query()->update(['is_occupied' => false]);
$ids = \App\Models\Product::whereNotNull('warehouse_location_id')->pluck('warehouse_location_id');
\App\Models\WarehouseLocation::whereIn('id', $ids)->update(['is_occupied' => true]);

echo "Synced " . count($ids) . " locations.\n";
