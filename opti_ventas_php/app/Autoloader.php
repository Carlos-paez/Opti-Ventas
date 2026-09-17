<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Autoloader PSR-4 sin dependencias externas.
 *
 * Mapea el prefijo de espacio de nombres `App\` al directorio `app/`
 * del proyecto, respetando la especificación PSR-4.
 *
 * Ejemplos:
 *   App\Core\Router          -> app/Core/Router.php
 *   App\Controllers\UserController -> app/Controllers/UserController.php
 */
final class Autoloader
{
    private const PREFIX = 'App\\';

    /**
     * Registra el autoloader en la cola de SPL.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    /**
     * Carga la clase solicitada resolviendo su ruta según PSR-4.
     *
     * @throws RuntimeException si la clase pertenece a `App\` pero su archivo no existe
     */
    public static function load(string $class): void
    {
        if (!str_starts_with($class, self::PREFIX)) {
            return;
        }

        $file = self::resolvePath($class);

        if (!is_file($file)) {
            throw new RuntimeException("Clase no encontrada: {$class}. Se esperaba en: {$file}");
        }

        require_once $file;
    }

    /**
     * Resuelve el nombre completo de una clase a su ruta absoluta.
     */
    public static function resolvePath(string $class): string
    {
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen(self::PREFIX)));

        return __DIR__ . DIRECTORY_SEPARATOR . $relative . '.php';
    }
}
