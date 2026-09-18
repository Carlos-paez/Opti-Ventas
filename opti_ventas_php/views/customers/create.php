<?php
use App\Core\View;

View::startSection('header');
?>
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Cliente</h2>
    <a href="<?= url('/customers') ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver
    </a>
</div>
<?php View::endSection(); ?>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <form method="POST" action="<?= url('/customers') ?>" class="space-y-6">
                <?= csrf_field() ?>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                    <input type="text" id="name" name="name" value="<?= e(old('name')) ?>" required autofocus
                           class="mt-1 block w-full rounded-md text-sm border <?= has_error('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if (has_error('name')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('name')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?= e(old('email')) ?>"
                           class="mt-1 block w-full rounded-md text-sm border <?= has_error('email') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if (has_error('email')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('email')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                    <input type="text" id="phone" name="phone" value="<?= e(old('phone')) ?>"
                           class="mt-1 block w-full rounded-md text-sm border <?= has_error('phone') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if (has_error('phone')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('phone')) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección</label>
                    <textarea id="address" name="address" rows="3"
                              class="mt-1 block w-full rounded-md text-sm border <?= has_error('address') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"><?= e(old('address')) ?></textarea>
                    <?php if (has_error('address')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('address')) ?></p><?php endif; ?>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?= url('/customers') ?>" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
