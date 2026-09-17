<?php
/** @var array $settings */
use App\Core\View;

View::startSection('header');
?>
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración</h2>
<?php View::endSection(); ?>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        <form method="POST" action="<?= url('/settings') ?>" class="p-6 space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del Negocio</label>
                <input type="text" id="business_name" name="business_name" value="<?= e(old('business_name', $settings['business_name'] ?? '')) ?>"
                       class="mt-1 block w-full rounded-md text-sm border <?= has_error('business_name') ? 'border-red-500' : 'border-gray-300' ?> focus:border-primary-500 focus:ring-primary-500">
                <p class="mt-1 text-xs text-gray-500">Nombre que aparecerá en los recibos y reportes.</p>
                <?php if (has_error('business_name')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('business_name')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tasa de Impuesto (%)</label>
                <input type="number" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100" value="<?= e(old('tax_rate', $settings['tax_rate'] ?? '')) ?>"
                       class="mt-1 block w-full rounded-md text-sm border <?= has_error('tax_rate') ? 'border-red-500' : 'border-gray-300' ?> focus:border-primary-500 focus:ring-primary-500">
                <p class="mt-1 text-xs text-gray-500">Porcentaje de impuesto aplicado a las ventas (ej. 16 para IVA 16%).</p>
                <?php if (has_error('tax_rate')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('tax_rate')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Moneda</label>
                <?php $currency = old('currency', $settings['currency'] ?? ''); ?>
                <select id="currency" name="currency"
                        class="mt-1 block w-full rounded-md text-sm border <?= has_error('currency') ? 'border-red-500' : 'border-gray-300' ?> focus:border-primary-500 focus:ring-primary-500">
                    <option value="MXN" <?= $currency === 'MXN' ? 'selected' : '' ?>>MXN - Peso Mexicano</option>
                    <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>USD - Dólar Americano</option>
                    <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                    <option value="COP" <?= $currency === 'COP' ? 'selected' : '' ?>>COP - Peso Colombiano</option>
                    <option value="ARS" <?= $currency === 'ARS' ? 'selected' : '' ?>>ARS - Peso Argentino</option>
                    <option value="CLP" <?= $currency === 'CLP' ? 'selected' : '' ?>>CLP - Peso Chileno</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Moneda utilizada para los precios y reportes.</p>
                <?php if (has_error('currency')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('currency')) ?></p><?php endif; ?>
            </div>

            <div>
                <label for="receipt_footer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pie de Recibo</label>
                <textarea id="receipt_footer" name="receipt_footer" rows="3" placeholder="¡Gracias por su compra!"
                          class="mt-1 block w-full rounded-md text-sm border <?= has_error('receipt_footer') ? 'border-red-500' : 'border-gray-300' ?> focus:border-primary-500 focus:ring-primary-500"><?= e(old('receipt_footer', $settings['receipt_footer'] ?? '')) ?></textarea>
                <p class="mt-1 text-xs text-gray-500">Mensaje que se imprime al final de cada recibo.</p>
                <?php if (has_error('receipt_footer')): ?><p class="mt-2 text-sm text-red-600"><?= e(field_error('receipt_footer')) ?></p><?php endif; ?>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
