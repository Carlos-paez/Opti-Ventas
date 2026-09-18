<?php

declare(strict_types=1);

require __DIR__ . '/../app/Autoloader.php';
App\Autoloader::register();
App\Core\Env::load(__DIR__ . '/../.env');
require __DIR__ . '/../app/helpers/config.php';

$config = require __DIR__ . '/../config/database.php';

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $config['host'],
    $config['port'],
    $config['database']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$catalogPath = __DIR__ . '/../Catalogo.md';
if (!is_file($catalogPath)) {
    fwrite(STDERR, "No se encontró el archivo de catálogo: {$catalogPath}\n");
    exit(1);
}

$lines = file($catalogPath, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    fwrite(STDERR, "No se pudo leer el archivo de catálogo.\n");
    exit(1);
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('DELETE FROM sale_items');
$pdo->exec('DELETE FROM stock_movements');
$pdo->exec('DELETE FROM products');
$pdo->exec('DELETE FROM categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$categoryColors = [
    '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#ef4444',
    '#8b5cf6', '#06b6d4', '#f97316', '#22c55e', '#eab308',
    '#3b82f6', '#84cc16', '#f43f5e', '#14b8a6', '#8b5cf6',
    '#a855f7', '#f59e0b', '#0ea5e9', '#10b981', '#f97316',
    '#e11d48', '#14b8a6', '#f59e0b',
];

$categoryNames = [];
$categoryIds = [];
$categoryIndex = 0;

$categoryStmt = $pdo->prepare(
    'INSERT INTO categories (name, slug, description, color, created_at, updated_at)
     VALUES (?, ?, ?, ?, NOW(), NOW())'
);

$productStmt = $pdo->prepare(
    'INSERT INTO products (name, slug, sku, barcode, category_id, description, price, cost, stock, stock_min, photo_path, is_active, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 1, NOW(), NOW())'
);

$currentCategory = null;
$usedSlugs = [];
$productCounter = 0;

foreach ($lines as $line) {
    $trimmed = trim($line);

    if (preg_match('/^##\s+(.*)$/', $trimmed, $matches)) {
        $currentCategory = trim($matches[1]);
        if ($currentCategory === '') {
            continue;
        }

        $slug = slugify($currentCategory);
        $baseSlug = $slug;
        $suffix = 2;
        while (isset($usedSlugs[$baseSlug])) {
            $baseSlug = $slug . '-' . $suffix;
            $suffix++;
        }
        $usedSlugs[$baseSlug] = true;

        $categoryNames[$currentCategory] = $currentCategory;
        $color = $categoryColors[$categoryIndex % count($categoryColors)];
        $categoryIndex++;
        $categoryStmt->execute([$currentCategory, $baseSlug, 'Producto de la categoría ' . $currentCategory, $color]);
        $categoryIds[$currentCategory] = (int) $pdo->lastInsertId();
        continue;
    }

    if ($currentCategory === null || !str_contains($trimmed, '|')) {
        continue;
    }

    if (preg_match('/^\|\s*[:\-\|\s]+\|?$/', $trimmed)) {
        continue;
    }

    if (preg_match('/^\|\s*N°\s*\|/i', $trimmed)) {
        continue;
    }

    $parts = preg_split('/\s*\|\s*/', trim($trimmed, " |\n\r"));
    if (count($parts) < 4) {
        continue;
    }

    $idValue = trim($parts[0]);
    if ($idValue === '' || !ctype_digit(str_replace(['#', ' '], '', $idValue))) {
        continue;
    }

    $name = trim($parts[1]);
    $details = trim($parts[2] ?? '');
    $priceRaw = trim($parts[3] ?? '');

    if ($name === '' || $priceRaw === '') {
        continue;
    }

    $price = parsePrice($priceRaw);
    if ($price === null) {
        continue;
    }

    $productCounter++;
    $slug = slugify($name);
    $baseSlug = $slug;
    $suffix = 2;
    while (isset($usedSlugs[$baseSlug])) {
        $baseSlug = $slug . '-' . $suffix;
        $suffix++;
    }
    $usedSlugs[$baseSlug] = true;

    $sku = sprintf('CAT-%04d', $productCounter);
    $barcode = sprintf('%013d', $productCounter);
    $stock = rand(10, 45);
    $cost = round($price * 0.6, 2);
    $description = $details === '' ? 'Producto del catálogo' : $details;

    $productStmt->execute([
        $name,
        $baseSlug,
        $sku,
        $barcode,
        $categoryIds[$currentCategory] ?? 1,
        $description,
        number_format((float) $price, 2, '.', ''),
        number_format((float) $cost, 2, '.', ''),
        $stock,
        5,
    ]);
}

$categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$productCountDb = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

echo "Categorías importadas: {$categoryCount}\n";
echo "Productos importados: {$productCountDb}\n";

echo "Importación terminada.\n";

function parsePrice(string $raw): ?float
{
    $clean = trim($raw);
    $clean = str_replace(['**', '*', '$', ' '], '', $clean);
    $clean = str_replace(['.', ','], ['.', ','], $clean);

    if ($clean === '') {
        return null;
    }

    if (str_contains($clean, ',')) {
        $parts = explode(',', $clean);
        if (count($parts) === 2) {
            $clean = $parts[0] . '.' . $parts[1];
        }
    }

    if (!is_numeric($clean)) {
        return null;
    }

    return (float) $clean;
}

function slugify(string $value): string
{
    $text = mb_strtolower(trim($value), 'UTF-8');
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ü' => 'u', 'ñ' => 'n', 'ç' => 'c', '°' => '',
    ]);
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
    $text = trim((string) $text, '-');

    return $text === '' ? 'producto' : $text;
}
