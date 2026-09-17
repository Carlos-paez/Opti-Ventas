<?php
/** @var array $product */
/** @var array $categories */
use App\Core\View;

View::startSection('header');
?>
<div class="flex items-center gap-3">
    <a href="<?= url('/products') ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Editar Producto</h2>
</div>
<?php View::endSection(); ?>

<div class="max-w-3xl">
    <form method="POST" action="<?= url('/products/' . (int) $product['id']) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Información General</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                    <input type="text" id="name" name="name" value="<?= e(old('name', $product['name'])) ?>" required
                           class="w-full px-3 py-2 text-sm border <?= has_error('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Nombre del producto">
                    <?php if (has_error('name')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('name')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                    <input type="text" id="sku" name="sku" value="<?= e(old('sku', $product['sku'])) ?>"
                           class="w-full px-3 py-2 text-sm border <?= has_error('sku') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="SKU-001">
                    <?php if (has_error('sku')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('sku')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de Barras</label>
                    <input type="text" id="barcode" name="barcode" value="<?= e(old('barcode', $product['barcode'])) ?>"
                           class="w-full px-3 py-2 text-sm border <?= has_error('barcode') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="1234567890123">
                    <?php if (has_error('barcode')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('barcode')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                    <select id="category_id" name="category_id"
                            class="w-full px-3 py-2 text-sm border <?= has_error('category_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Seleccionar categoría</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= (string) old('category_id', $product['category_id']) === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (has_error('category_id')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('category_id')) ?></p><?php endif; ?>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 text-sm border <?= has_error('description') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Descripción del producto"><?= e(old('description', $product['description'])) ?></textarea>
                    <?php if (has_error('description')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('description')) ?></p><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Precios e Inventario</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio de Venta</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input type="number" id="price" name="price" value="<?= e(old('price', $product['price'])) ?>" step="0.01" min="0" required
                               class="w-full pl-7 pr-3 py-2 text-sm border <?= has_error('price') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0.00">
                    </div>
                    <?php if (has_error('price')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('price')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Costo</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input type="number" id="cost" name="cost" value="<?= e(old('cost', $product['cost'])) ?>" step="0.01" min="0"
                               class="w-full pl-7 pr-3 py-2 text-sm border <?= has_error('cost') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0.00">
                    </div>
                    <?php if (has_error('cost')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('cost')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                    <input type="number" id="stock" name="stock" value="<?= e(old('stock', $product['stock'])) ?>" min="0" required
                           class="w-full px-3 py-2 text-sm border <?= has_error('stock') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <?php if (has_error('stock')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('stock')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="stock_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Mínimo</label>
                    <input type="number" id="stock_min" name="stock_min" value="<?= e(old('stock_min', $product['stock_min'])) ?>" min="0"
                           class="w-full px-3 py-2 text-sm border <?= has_error('stock_min') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <?php if (has_error('stock_min')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('stock_min')) ?></p><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Imagen y Estado</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto del Producto</label>
                    <div class="flex items-center gap-4">
                        <div id="photo-preview" class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden">
                            <?php if (!empty($product['photo_path'])): ?>
                                <img src="<?= e(url('/storage/' . $product['photo_path'])) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/*"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 cursor-pointer"
                                   onchange="previewPhoto(this)">
                            <p class="mt-1 text-xs text-gray-400">PNG, JPG, WEBP. Max 2MB</p>
                        </div>
                    </div>
                    <?php if (has_error('photo')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('photo')) ?></p><?php endif; ?>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5 mt-6">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?= old('is_active', $product['is_active']) ? 'checked' : '' ?>
                               class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 focus:ring-primary-500">
                    </div>
                    <label for="is_active" class="ml-3 text-sm mt-5">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Producto activo</span>
                        <span class="block text-gray-500 dark:text-gray-400">El producto estará disponible para venta</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="<?= url('/products') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                Actualizar Producto
            </button>
        </div>
    </form>
</div>

<script>
    function previewPhoto(input) {
        const preview = document.getElementById('photo-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
