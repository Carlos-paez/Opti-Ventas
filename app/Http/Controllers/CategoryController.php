<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->string('search')}%")
                    ->orWhere('description', 'like', "%{$request->string('search')}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:categories,name,{$category->id}"],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $category->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'No se puede eliminar una categoría que tiene productos asociados.']);
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
