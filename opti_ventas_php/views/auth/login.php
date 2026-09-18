<form method="POST" action="<?= url('/login') ?>">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico</label>
        <input id="email" type="email" name="email" value="<?= e(old('email')) ?>"
               required autofocus autocomplete="username"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                      <?= has_error('email') ? 'border-red-500' : '' ?>">
        <?php if (has_error('email')): ?>
            <p class="mt-1 text-sm text-red-600"><?= e(field_error('email')) ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
        <input id="password" type="password" name="password" required autocomplete="current-password"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white dark:bg-gray-700">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Recordarme</span>
        </label>
    </div>

    <div class="flex items-center justify-between mt-4">
        <a href="<?= url('/register') ?>" class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md">
            Registrarse
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            Iniciar sesión
        </button>
    </div>
</form>