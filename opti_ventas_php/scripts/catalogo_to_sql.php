<?php

declare(strict_types=1);

$catalogPath = __DIR__ . '/../Catalogo.md';
$outPath = __DIR__ . '/../database/import_products.sql';

if (!is_file($catalogPath)) {
    fwrite(STDERR, "No se encontró Catalogo.md en la ruta esperada: {$catalogPath}\n");
    exit(1);
}

$lines = file($catalogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    fwrite(STDERR, "No se pudo leer Catalogo.md\n");
    exit(1);
}

$sql = [];
$sql[] = "-- Script generado por scripts/catalogo_to_sql.php";
$sql[] = "SET FOREIGN_KEY_CHECKS=0;";
$sql[] = "DROP TABLE IF EXISTS sale_items;";
$sql[] = "DROP TABLE IF EXISTS stock_movements;";
$sql[] = "DROP TABLE IF EXISTS products;";
$sql[] = "DROP TABLE IF EXISTS categories;";

$sql[] = "CREATE TABLE categories (\n"
    . "  id INT AUTO_INCREMENT PRIMARY KEY,\n"
    . "  name VARCHAR(255) NOT NULL,\n"
    . "  slug VARCHAR(255) NOT NULL,\n"
    . "  description TEXT NULL,\n"
    . "  color VARCHAR(20) DEFAULT '#6366f1',\n"
    . "  created_at DATETIME NOT NULL,\n"
    . "  updated_at DATETIME NOT NULL\n"
    . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql[] = "CREATE TABLE products (\n"
    . "  id INT AUTO_INCREMENT PRIMARY KEY,\n"
    . "  name VARCHAR(255) NOT NULL,\n"
    . "  slug VARCHAR(255) NOT NULL,\n"
    . "  sku VARCHAR(64) NULL,\n"
    . "  barcode VARCHAR(64) NULL,\n"
    . "  category_id INT NULL,\n"
    . "  description TEXT NULL,\n"
    . "  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,\n"
    . "  cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,\n"
    . "  stock INT NOT NULL DEFAULT 0,\n"
    . "  stock_min INT NOT NULL DEFAULT 0,\n"
    . "  photo_path VARCHAR(255) NULL,\n"
    . "  is_active TINYINT(1) NOT NULL DEFAULT 1,\n"
    . "  created_at DATETIME NOT NULL,\n"
    . "  updated_at DATETIME NOT NULL,\n"
    . "  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL\n"
    . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql[] = "SET FOREIGN_KEY_CHECKS=1;";

$categoryIndex = 0;
$categoryIds = [];
$usedSlugs = [];
$productCounter = 0;

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

function sqlEscape(?string $v): string
{
    if ($v === null) {
        return 'NULL';
    }
    $v = str_replace("'", "''", $v);
    return "'" . $v . "'";
}

function parsePrice(string $raw): ?float
{
    $clean = trim($raw);
    $clean = str_replace(['**', '*', '$', ' '], '', $clean);
    if ($clean === '') {
        return null;
    }
    $clean = str_replace([','], ['.'], $clean);
    if (!is_numeric($clean)) {
        return null;
    }
    return (float) $clean;
}

$currentCategory = null;
$categoryColors = [
    '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#ef4444',
    '#8b5cf6', '#06b6d4', '#f97316', '#22c55e', '#eab308',
];

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (preg_match('/^##\s+(.*)$/', $trimmed, $m)) {
        $currentCategory = trim($m[1]);
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
        $categoryIndex++;
        $color = $categoryColors[$categoryIndex % count($categoryColors)];
        $created = $updated = date('Y-m-d H:i:s');
        $sql[] = sprintf(
            "INSERT INTO categories (name, slug, description, color, created_at, updated_at) VALUES (%s, %s, %s, %s, '%s', '%s');",
            sqlEscape($currentCategory), sqlEscape($baseSlug), sqlEscape('Categoria importada desde Catalogo.md'), sqlEscape($color), $created, $updated
        );
        // simulate id
        $categoryIds[$currentCategory] = count($categoryIds) + 1;
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
    $barcode = str_pad((string)$productCounter, 13, '0', STR_PAD_LEFT);
    $stock = rand(10, 45);
    $cost = round($price * 0.6, 2);
    $description = $details === '' ? 'Producto del catálogo' : $details;
    $created = $updated = date('Y-m-d H:i:s');

    $categoryId = $categoryIds[$currentCategory] ?? 'NULL';
    $sql[] = sprintf(
        "INSERT INTO products (name, slug, sku, barcode, category_id, description, price, cost, stock, stock_min, photo_path, is_active, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %d, %d, NULL, 1, '%s', '%s');",
        sqlEscape($name), sqlEscape($baseSlug), sqlEscape($sku), sqlEscape($barcode), is_int($categoryId) ? $categoryId : 'NULL', sqlEscape($description), number_format($price, 2, '.', ''), number_format($cost, 2, '.', ''), $stock, 5, $created, $updated
    );
}

$sql[] = "-- Fin del script";

file_put_contents($outPath, implode("\n", $sql));

echo "Generado: {$outPath}\n";

exit(0);
