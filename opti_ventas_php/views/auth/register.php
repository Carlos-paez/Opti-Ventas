<form method="POST" action="<?= url('/register') ?>">
    <?= csrf_field() ?>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input id="name" type="text" name="name" value="<?= e(old('name')) ?>"
               required autofocus autocomplete="name"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                      <?= has_error('name') ? 'border-red-500' : '' ?>">
        <?php if (has_error('name')): ?>
            <p class="mt-1 text-sm text-red-600"><?= e(field_error('name')) ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
        <input id="email" type="email" name="email" value="<?= e(old('email')) ?>"
               required autocomplete="username"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                      <?= has_error('email') ? 'border-red-500' : '' ?>">
        <?php if (has_error('email')): ?>
            <p class="mt-1 text-sm text-red-600"><?= e(field_error('email')) ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                      <?= has_error('password') ? 'border-red-500' : '' ?>">
        <?php if (has_error('password')): ?>
            <p class="mt-1 text-sm text-red-600"><?= e(field_error('password')) ?></p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="block mt-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between mt-4">
        <a href="<?= url('/login') ?>" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md">
            Ya tengo una cuenta
        </a>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            Registrarse
        </button>
    </div>
</form>