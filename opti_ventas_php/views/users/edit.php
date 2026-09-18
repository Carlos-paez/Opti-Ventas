<?php
/** @var array $user */
use App\Core\View;

View::startSection('header');
?>
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuario</h2>
<?php View::endSection(); ?>

<div class="max-w-2xl">
    <div class="mb-4">
        <a href="<?= url('/users') ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
        <form method="POST" action="<?= url('/users/' . (int) $user['id']) ?>" class="p-6 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                <input type="text" id="name" name="name" value="<?= e(old('name', $user['name'])) ?>" required autofocus autocomplete="name"
                       class="mt-1 block w-full rounded-md text-sm border <?= has_error('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                <?php if (has_error('name')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('name')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required autocomplete="username"
                       class="mt-1 block w-full rounded-md text-sm border <?= has_error('email') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                <?php if (has_error('email')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('email')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rol</label>
                <?php $role = old('role', $user['role']); ?>
                <select id="role" name="role"
                        class="mt-1 block w-full rounded-md text-sm border <?= has_error('role') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <option value="seller" <?= $role === 'seller' ? 'selected' : '' ?>>Vendedor</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <?php if (has_error('role')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('role')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña</label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                       class="mt-1 block w-full rounded-md text-sm border <?= has_error('password') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dejar en blanco para mantener la contraseña actual.</p>
                <?php if (has_error('password')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('password')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                       class="mt-1 block w-full rounded-md text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="<?= url('/users') ?>" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
