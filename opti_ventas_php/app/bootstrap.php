<?php

declare(strict_types=1);

/**
 * Arranque de la aplicación.
 *
 * Orden de inicialización:
 *   1. Autoloader PSR-4 (clases App\)
 *   2. Variables de entorno (.env)
 *   3. Funciones helper globales
 *   4. Configuración (config/app.php + config/database.php)
 *   5. Manejo de errores según APP_DEBUG
 *   6. Sesión
 */

require __DIR__ . '/Autoloader.php';
App\Autoloader::register();

App\Core\Env::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

require __DIR__ . '/helpers/paths.php';
require __DIR__ . '/helpers/config.php';
require __DIR__ . '/helpers/output.php';
require __DIR__ . '/helpers/url.php';
require __DIR__ . '/helpers/request.php';
require __DIR__ . '/helpers/session.php';
require __DIR__ . '/helpers/csrf.php';
require __DIR__ . '/helpers/format.php';

$GLOBALS['__config'] = [
    'app' => require config_path('app.php'),
    'database' => require config_path('database.php'),
];

if (config('app.debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
