<?php
use App\Core\View;

View::startSection('header');
?>
<div class="flex items-center gap-3">
    <a href="<?= url('/categories') ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nueva Categoría</h2>
</div>
<?php View::endSection(); ?>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="<?= url('/categories') ?>">
            <?= csrf_field() ?>

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                    <input type="text" id="name" name="name" value="<?= e(old('name')) ?>" required
                           class="w-full px-3 py-2 text-sm border <?= has_error('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Nombre de la categoría">
                    <?php if (has_error('name')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('name')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 text-sm border <?= has_error('description') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Descripción opcional"><?= e(old('description')) ?></textarea>
                    <?php if (has_error('description')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('description')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="color" name="color" value="<?= e(old('color', '#3b82f6')) ?>"
                               class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                        <span id="color-value" class="text-sm text-gray-500 dark:text-gray-400 font-mono"><?= e(old('color', '#3b82f6')) ?></span>
                    </div>
                    <?php if (has_error('color')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e(field_error('color')) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="<?= url('/categories') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    Crear Categoría
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('color').addEventListener('input', function () {
        document.getElementById('color-value').textContent = this.value;
    });
</script>
