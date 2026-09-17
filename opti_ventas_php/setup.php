<?php

declare(strict_types=1);

/**
 * Instalador CLI del proyecto Opti Ventas (PHP puro).
 *
 * Uso: php setup.php
 *
 * Crea la base de datos, el esquema y los datos de ejemplo.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/app/Autoloader.php';
App\Autoloader::register();
App\Core\Env::load(__DIR__ . '/.env');

$config = ['db' => require __DIR__ . '/config/database.php'] + ['debug' => true];

echo "=== Opti Ventas - Instalador ===\n\n";

$host = $config['db']['host'];
$port = $config['db']['port'];
$database = $config['db']['database'];
$user = $config['db']['username'];
$password = $config['db']['password'];

echo "Conectando a MySQL...\n";

$server = new PDO(
    "mysql:host={$host};port={$port};charset=utf8mb4",
    $user,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Creando base de datos '{$database}' (si no existe)...\n";
$server->exec(
    "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
);
$server->exec("USE `{$database}`");

echo "Creando esquema...\n";
$schema = file_get_contents(__DIR__ . '/database/schema.sql');
$server->exec($schema);

echo "Verificando que no existan datos previos...\n";
$hasUsers = (int) $server->query('SELECT COUNT(*) FROM users')->fetchColumn();

if ($hasUsers > 0) {
    echo "La base de datos ya contiene datos. No se ejecutó el seed.\n\n";
    echo "Listo. Abre el proyecto en Laragon (http://opti_ventas_php.test/)\n";
    echo "o inicia el servidor embebido con:\n";
    echo "  php -S localhost:8000 index.php\n\n";

    exit(0);
}

$adminHash = password_hash('password', PASSWORD_DEFAULT);
$sellerHash = password_hash('password', PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');

$seed = $server->prepare(
    'INSERT INTO users (name, email, email_verified_at, password, role, is_active, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, 1, ?, ?)'
);
$seed->execute(['Administrador', 'admin@optiventas.com', $now, $adminHash, 'admin', $now, $now]);
$seed->execute(['Vendedor Test', 'vendedor@optiventas.com', $now, $sellerHash, 'seller', $now, $now]);

$categories = [
    ['name' => 'Electrónica', 'description' => 'Dispositivos y gadgets', 'color' => '#6366f1'],
    ['name' => 'Ropa', 'description' => 'Vestimenta y accesorios', 'color' => '#ec4899'],
    ['name' => 'Hogar', 'description' => 'Artículos para el hogar', 'color' => '#f59e0b'],
    ['name' => 'Deportes', 'description' => 'Artículos deportivos', 'color' => '#10b981'],
    ['name' => 'Alimentos', 'description' => 'Bebidas y snacks', 'color' => '#ef4444'],
];

$catStmt = $server->prepare(
    'INSERT INTO categories (name, slug, description, color, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?)'
);

foreach ($categories as $cat) {
    $catStmt->execute([$cat['name'], slug($cat['name']), $cat['description'], $cat['color'], $now, $now]);
}

$products = [
    ['name' => 'Audífonos Bluetooth', 'sku' => 'AUD-001', 'price' => 299.99, 'cost' => 150, 'stock' => 50, 'category_id' => 1],
    ['name' => 'Cable USB-C 2m', 'sku' => 'CAB-001', 'price' => 49.99, 'cost' => 20, 'stock' => 200, 'category_id' => 1],
    ['name' => 'Camiseta Básica', 'sku' => 'ROP-001', 'price' => 199.99, 'cost' => 80, 'stock' => 100, 'category_id' => 2],
    ['name' => 'Jeans Slim Fit', 'sku' => 'ROP-002', 'price' => 599.99, 'cost' => 280, 'stock' => 60, 'category_id' => 2],
    ['name' => 'Lámpara LED', 'sku' => 'HOG-001', 'price' => 349.99, 'cost' => 160, 'stock' => 40, 'category_id' => 3],
    ['name' => 'Set de Cocina 5 pzas', 'sku' => 'HOG-002', 'price' => 899.99, 'cost' => 450, 'stock' => 25, 'category_id' => 3],
    ['name' => 'Balón de Fútbol', 'sku' => 'DEP-001', 'price' => 249.99, 'cost' => 100, 'stock' => 80, 'category_id' => 4],
    ['name' => 'Mancuernas 10kg', 'sku' => 'DEP-002', 'price' => 399.99, 'cost' => 180, 'stock' => 30, 'category_id' => 4],
    ['name' => 'Agua Mineral 600ml', 'sku' => 'ALI-001', 'price' => 15.99, 'cost' => 6, 'stock' => 500, 'category_id' => 5],
    ['name' => 'Chips Fritos 150g', 'sku' => 'ALI-002', 'price' => 29.99, 'cost' => 14, 'stock' => 300, 'category_id' => 5],
    ['name' => 'Mouse Inalámbrico', 'sku' => 'AUD-002', 'price' => 189.99, 'cost' => 90, 'stock' => 75, 'category_id' => 1],
    ['name' => 'Teclado Mecánico', 'sku' => 'AUD-003', 'price' => 699.99, 'cost' => 350, 'stock' => 2, 'category_id' => 1],
];

$prodStmt = $server->prepare(
    'INSERT INTO products (name, slug, sku, barcode, category_id, description, price, cost, stock, stock_min, is_active, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, 5, 1, ?, ?)'
);

foreach ($products as $p) {
    $barcode = str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    $prodStmt->execute([$p['name'], slug($p['name']), $p['sku'], $barcode, $p['category_id'], $p['price'], $p['cost'], $p['stock'], $now, $now]);
}

$settings = [
    ['business_name', 'Opti Ventas'],
    ['tax_rate', '16'],
    ['currency', 'MXN'],
    ['receipt_footer', '¡Gracias por su compra!'],
];

$setStmt = $server->prepare(
    'INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, ?, ?)'
);

foreach ($settings as $s) {
    $setStmt->execute([$s[0], $s[1], $now, $now]);
}

echo "Creando directorio de almacenamiento...\n";
$storage = __DIR__ . '/storage/products';
if (!is_dir($storage)) {
    mkdir($storage, 0777, true);
}

echo "\n=== Instalación completada ===\n\n";
echo "Usuarios:\n";
echo "  Admin:  admin@optiventas.com / password\n";
echo "  Venta:   vendedor@optiventas.com / password\n\n";
echo "Con Laragon, abre directamente el host del proyecto:\n";
echo "  http://opti_ventas_php.test/\n\n";
echo "O inicia el servidor embebido de PHP:\n";
echo "  php -S localhost:8000 index.php\n\n";

/**
 * Convierte texto a slug (misma lógica que la app original).
 */
function slug(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ü' => 'u', 'ñ' => 'n', 'ç' => 'c',
    ]);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim((string) $text, '-');
}