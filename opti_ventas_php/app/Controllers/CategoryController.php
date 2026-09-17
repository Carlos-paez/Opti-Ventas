<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Category;

final class CategoryController
{
    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $filters = [
            'search' => trim((string) query('search', '')),
        ];

        $paged = Category::all($filters, $page, 15);

        View::render('categories/index', [
            'categories' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => 15,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        View::render('categories/create');
    }

    public function store(): void
    {
        $data = [
            'name' => trim((string) input('name')),
            'description' => trim((string) input('description')),
            'color' => trim((string) input('color', '#6366f1')),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/categories/create');
        }

        $data['slug'] = slugify($data['name']);
        Category::create($data);

        flash('success', 'Categoría creada correctamente.');
        redirect('/categories');
    }

    public function edit(int $id): void
    {
        $category = Category::find($id);

        if ($category === null) {
            flash('error', 'La categoría no existe.');
            redirect('/categories');
        }

        View::render('categories/edit', ['category' => $category]);
    }

    public function update(int $id): void
    {
        $category = Category::find($id);

        if ($category === null) {
            flash('error', 'La categoría no existe.');
            redirect('/categories');
        }

        $data = [
            'name' => trim((string) input('name')),
            'description' => trim((string) input('description')),
            'color' => trim((string) input('color', '#6366f1')),
        ];

        $errors = Validator::validate($data, [
            'name' => "required|string|max:255|unique:categories,name,{$id}",
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/categories/' . $id . '/edit');
        }

        $data['slug'] = slugify($data['name']);
        Category::update($id, $data);

        flash('success', 'Categoría actualizada correctamente.');
        redirect('/categories');
    }

    public function destroy(int $id): void
    {
        if (Category::find($id) === null) {
            flash('error', 'La categoría no existe.');
            back();
        }

        if (Category::hasProducts($id)) {
            flash('error', 'No se puede eliminar una categoría que tiene productos asociados.');
            back();
        }

        Category::delete($id);

        flash('success', 'Categoría eliminada correctamente.');
        redirect('/categories');
    }
}