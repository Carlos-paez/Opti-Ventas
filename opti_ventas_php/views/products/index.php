<?php
/** @var array $products */
/** @var array $categories */
/** @var array $filters */
/** @var int $total */
/** @var int $page */
/** @var int $perPage */
use App\Core\View;

View::startSection('header');
?>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Productos</h2>
    <a href="<?= url('/products/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo Producto
    </a>
</div>
<?php View::endSection(); ?>

<form method="GET" action="<?= url('/products') ?>" class="mb-6">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Buscar productos..."
                   class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <select name="category_id" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Todas las categorías</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= (string) $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Todos los estados</option>
            <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Activos</option>
            <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactivos</option>
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-gray-600 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-500 transition-colors">
            Filtrar
        </button>
    </div>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
    <?php if (!$products): ?>
        <div class="col-span-full py-16 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="text-gray-500 dark:text-gray-400">No se encontraron productos</p>
        </div>
    <?php endif; ?>
    <?php foreach ($products as $product): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-video bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <?php if (!empty($product['photo_path'])): ?>
                    <img src="<?= e(url('/storage/' . $product['photo_path'])) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <?php endif; ?>
            </div>

            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= e($product['name']) ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono"><?= e($product['sku']) ?></p>
                    </div>
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <?= e($product['category_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <span class="text-lg font-bold text-gray-900 dark:text-white"><?= e(money((float) $product['price'])) ?></span>
                    <?php if ((int) $product['stock'] === 0): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Sin stock</span>
                    <?php elseif ((int) $product['stock'] <= (int) ($product['stock_min'] ?? 5)): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Stock bajo (<?= e($product['stock']) ?>)</span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400"><?= e($product['stock']) ?> uds</span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?= url('/products/' . (int) $product['id'] . '/edit') ?>" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                    <form method="POST" action="<?= url('/products/' . (int) $product['id']) ?>" onsubmit="return confirm('¿Eliminar este producto?')" class="flex-1">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include views_path('partials/pagination.php'); ?>
