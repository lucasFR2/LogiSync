<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Check for exclusive location conflict
            if (!empty($data['warehouse_location_id'])) {
                $location = \App\Models\WarehouseLocation::find($data['warehouse_location_id']);
                if ($location && $location->is_occupied) {
                    throw new \Exception("Esta posição já está ocupada.");
                }
            }

            $product = Product::create($data);

            if ($product->warehouse_location_id) {
                \App\Models\WarehouseLocation::where('id', $product->warehouse_location_id)
                    ->update(['is_occupied' => true]);
            }

            if ($product->quantity > 0) {
                Inventory::create([
                    'product_id' => $product->id,
                    'quantity'   => $product->quantity,
                    'type'       => 'entrada',
                    'status'     => 'confirmada',
                    'notes'      => 'Entrada inicial - Cadastro do produto',
                    'user_id'    => Auth::id(),
                ]);
            }

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $newLocId = $data['warehouse_location_id'] ?? null;
            $oldLocId = $product->warehouse_location_id;

            if ($newLocId && $newLocId != $oldLocId) {
                $location = \App\Models\WarehouseLocation::find($newLocId);
                if ($location && $location->is_occupied) {
                    $occupant = Product::where('warehouse_location_id', $location->id)->where('id', '!=', $product->id)->first();
                    if ($occupant) {
                        throw new \Exception("Localização ocupada pelo produto: " . $occupant->name);
                    }
                }
            }

            // Release old location
            if ($oldLocId && $oldLocId != $newLocId) {
                \App\Models\WarehouseLocation::where('id', $oldLocId)
                    ->update(['is_occupied' => false]);
            }

            $product->update($data);

            // Mark new location as occupied
            if ($newLocId && $newLocId != $oldLocId) {
                \App\Models\WarehouseLocation::where('id', $newLocId)
                    ->update(['is_occupied' => true]);
            }

            return $product;
        });
    }
}
