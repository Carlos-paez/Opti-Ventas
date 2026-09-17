<?php
/** @var array $summary */
/** @var string $startDate */
/** @var string $endDate */
/** @var array $topProducts */
use App\Core\View;

View::startSection('header');
?>
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
<?php View::endSection(); ?>

<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form method="GET" action="<?= url('/reports') ?>" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[180px]">
                <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Inicio</label>
                <input type="date" id="from" name="from" value="<?= e($startDate) ?>"
                       class="mt-1 block w-full rounded-md text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Fin</label>
                <input type="date" id="to" name="to" value="<?= e($endDate) ?>"
                       class="mt-1 block w-full rounded-md text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                Filtrar
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Ventas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= e(number_format($summary['totalSales'])) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ingresos Totales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= e(money((float) $summary['totalRevenue'])) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-amber-100 text-amber-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ticket Promedio</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= e(money((float) $summary['avgTicket'])) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ganancia</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= e(money((float) $summary['totalProfit'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Productos Más Vendidos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">Producto</th>
                        <th scope="col" class="px-6 py-3">Unidades Vendidas</th>
                        <th scope="col" class="px-6 py-3 text-right">Ingresos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (!$topProducts): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No hay datos de ventas para el período seleccionado.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($topProducts as $index => $product): ?>
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= e($index + 1) ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= e($product['name']) ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= e($product['total_quantity']) ?></td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white"><?= e(money((float) $product['total_revenue'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
