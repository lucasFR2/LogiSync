<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\Logger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
            'name.max'      => 'O nome da categoria não pode exceder 100 caracteres',
        ]);

        $category = Category::create($validated);
        Logger::log('create_category', "O usuário criou a categoria: {$category->name} (#{$category->id})");

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
        ]);

        $category->update($validated);
        Logger::log('update_category', "O usuário alterou a categoria: {$category->name} (#{$category->id})");

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $catName = $category->name;
        $catId = $category->id;
        $category->delete();
        Logger::log('delete_category', "O usuário removeu a categoria: {$catName} (#{$catId})");

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Quick-create via AJAX (used in product forms).
     */
    public function storeQuick(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
        ]);

        $category = Category::create($validated);
        Logger::log('create_category', "O usuário criou a categoria (rápido): {$category->name} (#{$category->id})");

        return response()->json([
            'success'     => true,
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description ?? '',
            'message'     => 'Categoria criada com sucesso!',
        ]);
    }
}
