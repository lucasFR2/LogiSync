<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Verificação Volumétrica ===\n\n";

// Localizações
$locCount = \App\Models\WarehouseLocation::count();
echo "Total de localizações: {$locCount}\n";

$loc = \App\Models\WarehouseLocation::first();
if ($loc) {
    echo "Primeira localização: {$loc->full_code}\n";
    echo "  W={$loc->width} H={$loc->height} D={$loc->depth}\n";
    echo "  Volume total: " . $loc->totalVolume() . " u³\n";
    echo "  Volume usado: " . $loc->usedVolume() . " u³\n";
    echo "  Volume disponível: " . $loc->availableVolume() . " u³\n";
    echo "  Ocupação: " . $loc->occupancyPercent() . "%\n";
} else {
    echo "  (sem localizações)\n";
}

echo "\n";

// Produtos
$prodWithDims = \App\Models\Product::whereNotNull('width')->whereNotNull('height')->whereNotNull('depth')->count();
echo "Produtos com dimensões: {$prodWithDims} / " . \App\Models\Product::count() . "\n";

$prod = \App\Models\Product::whereNotNull('width')->first();
if ($prod) {
    echo "Produto: {$prod->name}\n";
    echo "  W={$prod->width} H={$prod->height} D={$prod->depth}\n";
    echo "  Volume unitário: " . $prod->unitVolume() . " u³\n";
    echo "  Volume total (qty={$prod->quantity}): " . $prod->totalVolume($prod->quantity) . " u³\n";
}

echo "\n=== OK ===\n";
