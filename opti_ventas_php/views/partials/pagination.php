<?php
/** @var int $page */
/** @var int $total */
/** @var int $perPage */
$lastPage = (int) max(1, ceil($total / max(1, $perPage)));

if ($lastPage <= 1) {
    return;
}

$params = $_GET;
unset($params['page']);

$pageUrl = fn (int $p): string => $params ? '?' . http_build_query(array_merge($params, ['page' => $p])) : '?page=' . $p;

$start = max(1, $page - 2);
$end = min($lastPage, $page + 2);
$pagesList = range($start, $end);
?>
<div class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Mostrando <?= e(((int) ($page - 1) * $perPage) + 1) ?> - <?= e(min((int) $page * $perPage, (int) $total)) ?> de <?= e((int) $total) ?>
    </p>
    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Paginación">
        <?php if ($page > 1): ?>
            <a href="<?= e($pageUrl($page - 1)) ?>"
               class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium">
                <span class="sr-only">Anterior</span>
                &lsaquo;
            </a>
        <?php endif; ?>

        <?php foreach ($pagesList as $p): ?>
            <a href="<?= e($pageUrl($p)) ?>"
               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium
                      <?= $p === $page
                          ? 'border-primary-500 bg-primary-600 text-white'
                          : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
                <?= e((int) $p) ?>
            </a>
        <?php endforeach; ?>

        <?php if ($page < $lastPage): ?>
            <a href="<?= e($pageUrl($page + 1)) ?>"
               class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium">
                <span class="sr-only">Siguiente</span>
                &rsaquo;
            </a>
        <?php endif; ?>
    </nav>
</div>