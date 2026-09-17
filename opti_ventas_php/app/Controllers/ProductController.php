<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;

final class ProductController
{
    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $filters = [
            'search' => trim((string) query('search', '')),
            'category_id' => query('category_id', '') !== '' ? (int) query('category_id') : null,
            'status' => query('status', '') !== '' ? (string) query('status') : null,
        ];

        $categoryId = $filters['category_id'];
        $filters['category_id'] = $categoryId;

        $paged = Product::all($filters, $page, 15);

        View::render('products/index', [
            'products' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => 15,
            'categories' => Category::allOrdered(),
            'filters' => array_merge($filters, ['category_id' => $categoryId === null ? '' : (string) $categoryId]),
        ]);
    }

    public function create(): void
    {
        View::render('products/create', [
            'categories' => Category::allOrdered(),
        ]);
    }

    public function store(): void
    {
        $data = [
            'name' => trim((string) input('name')),
            'sku' => nullable_string(input('sku')),
            'barcode' => nullable_string(input('barcode')),
            'category_id' => input('category_id', '') !== '' ? (int) input('category_id') : null,
            'description' => nullable_string(input('description')),
            'price' => input('price'),
            'cost' => input('cost', '') !== '' ? (float) input('cost') : 0,
            'stock' => (int) input('stock', 0),
            'stock_min' => input('stock_min', '') !== '' ? (int) input('stock_min') : 5,
            'is_active' => (int) (bool) input('is_active', false),
        ];

        $photoError = null;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoError = $this->handlePhoto($_FILES['photo']);
        }

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_min' => 'nullable|integer|min:0',
        ]);

        if ($photoError !== null) {
            $errors['photo'] = $photoError;
        }

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/products/create');
        }

        $data['slug'] = slugify($data['name']);

        if ($photoError === null && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $data['photo_path'] = $this->storePhoto($_FILES['photo']);
        }

        Product::create($data);

        flash('success', 'Producto creado correctamente.');
        redirect('/products');
    }

    public function edit(int $id): void
    {
        $product = Product::find($id);

        if ($product === null) {
            flash('error', 'El producto ya no existe o fue eliminado.');
            redirect('/products');
        }

        View::render('products/edit', [
            'product' => $product,
            'categories' => Category::allOrdered(),
        ]);
    }

    public function update(int $id): void
    {
        $product = Product::find($id);

        if ($product === null) {
            flash('error', 'El producto ya no existe o fue eliminado.');
            redirect('/products');
        }

        $data = [
            'name' => trim((string) input('name')),
            'sku' => nullable_string(input('sku')),
            'barcode' => nullable_string(input('barcode')),
            'category_id' => input('category_id', '') !== '' ? (int) input('category_id') : null,
            'description' => nullable_string(input('description')),
            'price' => input('price'),
            'cost' => input('cost', '') !== '' ? (float) input('cost') : 0,
            'stock' => (int) input('stock', 0),
            'stock_min' => input('stock_min', '') !== '' ? (int) input('stock_min') : 5,
            'is_active' => (int) (bool) input('is_active', false),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'sku' => "nullable|string|max:100|unique:products,sku,{$id}",
            'barcode' => "nullable|string|max:100|unique:products,barcode,{$id}",
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_min' => 'nullable|integer|min:0',
        ]);

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoError = $this->handlePhoto($_FILES['photo']);

            if ($photoError !== null) {
                $errors['photo'] = $photoError;
            }
        }

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/products/' . $id . '/edit');
        }

        $data['slug'] = $product['slug'] ?: slugify($data['name']);

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            if (!empty($product['photo_path'])) {
                @unlink(storage_path($product['photo_path']));
            }

            $data['photo_path'] = $this->storePhoto($_FILES['photo']);
        }

        Product::update($id, $data);

        flash('success', 'Producto actualizado correctamente.');
        redirect('/products');
    }

    public function destroy(int $id): void
    {
        $product = Product::find($id);

        if ($product === null) {
            flash('error', 'El producto ya no existe o fue eliminado.');
            back();
        }

        if (Product::hasSaleItems($id)) {
            flash('error', 'No se puede eliminar un producto que ya tiene ventas asociadas.');
            back();
        }

        if (!empty($product['photo_path'])) {
            @unlink(storage_path($product['photo_path']));
        }

        Product::delete($id);

        flash('success', 'Producto eliminado correctamente.');
        redirect('/products');
    }

    private function handlePhoto(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'No se pudo subir la imagen.';
        }

        if ((int) $file['size'] > 2 * 1024 * 1024) {
            return 'La imagen no debe superar los 2MB.';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return 'La imagen debe ser JPG, PNG o WEBP.';
        }

        return null;
    }

    private function storePhoto(array $file): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $ext = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $name = 'products/' . bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = storage_path($name);

        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        move_uploaded_file($file['tmp_name'], $destination);

        return $name;
    }
}