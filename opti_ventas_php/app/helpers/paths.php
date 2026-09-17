<?php

declare(strict_types=1);

/**
 * Resolución de rutas del proyecto.
 *
 * Todas las rutas se resuelven respecto a la raíz del proyecto,
 * donde vive este front controller.
 */

function base_path(string $path = ''): string
{
    return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

function app_path(string $path = ''): string
{
    return base_path('app' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

function views_path(string $path = ''): string
{
    return base_path('views' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

function storage_path(string $path = ''): string
{
    return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

function config_path(string $path = ''): string
{
    return base_path('config' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}
