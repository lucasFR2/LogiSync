<?php

namespace App\Http\Controllers;

use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseLocationController extends Controller
{
    public function index()
    {
        $locations = WarehouseLocation::withCount('products')
            ->orderBy('aisle')
            ->orderBy('column')
            ->orderBy('level')
            ->paginate(50);

        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aisle' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
        ]);

        $fullCode = strtoupper($request->aisle . '-' . $request->column . '-' . $request->level);

        // Check for duplicates
        if (WarehouseLocation::where('full_code', $fullCode)->exists()) {
            return back()->with('error', "A localização {$fullCode} já existe.");
        }

        WarehouseLocation::create([
            'aisle' => strtoupper($request->aisle),
            'column' => strtoupper($request->column),
            'level' => strtoupper($request->level),
            'full_code' => $fullCode,
            'is_occupied' => false,
            'allow_shared' => true,
            'width' => $request->width,
            'height' => $request->height,
            'depth' => $request->depth,
            'max_weight' => $request->max_weight,
        ]);

        return redirect()->route('locations.index')->with('success', 'Localização criada com sucesso!');
    }

    public function update(Request $request, WarehouseLocation $location)
    {
        $request->validate([
            'aisle' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
        ]);

        $fullCode = strtoupper($request->aisle . '-' . $request->column . '-' . $request->level);

        // Check for duplicates
        if (WarehouseLocation::where('full_code', $fullCode)->where('id', '!=', $location->id)->exists()) {
            return back()->with('error', "A localização {$fullCode} já existe.");
        }

        $location->update([
            'aisle' => strtoupper($request->aisle),
            'column' => strtoupper($request->column),
            'level' => strtoupper($request->level),
            'full_code' => $fullCode,
            'width' => $request->width,
            'height' => $request->height,
            'depth' => $request->depth,
            'max_weight' => $request->max_weight,
        ]);

        return redirect()->route('locations.index')->with('success', 'Localização atualizada com sucesso!');
    }

    public function destroy(WarehouseLocation $location)
    {
        if ($location->products()->exists()) {
            return back()->with('error', 'Não é possível remover uma localização que possui produtos.');
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Localização removida com sucesso!');
    }

    /**
     * Bulk generator for warehouse locations
     */
    public function generate(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string|max:5',
            'aisles_count' => 'required|integer|min:1|max:50',
            'columns_count' => 'required|integer|min:1|max:50',
            'levels_count' => 'required|integer|min:1|max:10',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
        ]);

        $prefix = strtoupper($request->prefix);
        $count = 0;

        for ($a = 1; $a <= $request->aisles_count; $a++) {
            $aisle = $prefix . str_pad($a, 2, '0', STR_PAD_LEFT);
            for ($c = 1; $c <= $request->columns_count; $c++) {
                $column = str_pad($c, 2, '0', STR_PAD_LEFT);
                for ($l = 1; $l <= $request->levels_count; $l++) {
                    $level = 'N' . $l;
                    $fullCode = "{$aisle}-{$column}-{$level}";

                    WarehouseLocation::firstOrCreate(
                        ['full_code' => $fullCode],
                        [
                            'aisle' => $aisle,
                            'column' => $column,
                            'level' => $level,
                            'is_occupied' => false,
                            'allow_shared' => true,
                            'width' => $request->width,
                            'height' => $request->height,
                            'depth' => $request->depth,
                            'max_weight' => $request->max_weight,
                        ]
                    );
                    $count++;
                }
            }
        }

        return redirect()->route('locations.index')->with('success', "{$count} localizações geradas/verificadas com sucesso.");
    }
}
