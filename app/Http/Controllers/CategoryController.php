<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\Logger;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:categorias.gerenciar'),
        ];
    }

    public function index(Request $request)
    {
        $query = Category::with('parent');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('parent', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $categories = $query->orderBy('name')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'parent_id'   => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
            'name.max'      => 'O nome da categoria não pode exceder 100 caracteres',
            'parent_id.exists' => 'O grupo selecionado é inválido',
        ]);

        $category = Category::create($validated);
        Logger::log('create_category', "O usuário criou a categoria: {$category->name} (#{$category->id})");

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'parent_id'   => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
            'parent_id.exists' => 'O grupo selecionado é inválido',
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
            'parent_id'   => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique'   => 'Já existe uma categoria com esse nome',
            'parent_id.exists' => 'O grupo selecionado é inválido',
        ]);

        $category = Category::create($validated);
        Logger::log('create_category', "O usuário criou a categoria (rápido): {$category->name} (#{$category->id})");

        return response()->json([
            'success'     => true,
            'id'          => $category->id,
            'name'        => $category->name,
            'parent_id'   => $category->parent_id,
            'parent_name' => $category->parent ? $category->parent->name : null,
            'description' => $category->description ?? '',
            'message'     => 'Categoria criada com sucesso!',
        ]);
    }
}
