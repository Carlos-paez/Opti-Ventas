<?php

declare(strict_types=1);

/**
 * Generación de URLs.
 *
 * Detecta automáticamente el host y el subdirectorio donde está
 * instalada la aplicación (útil tanto para el host de Laragon,
 * p. ej. http://opti_ventas_php.test/, como para un subdirectorio).
 */

function scheme(): string
{
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        return strtolower(trim(explode(',', (string) $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
    }

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }

    return 'http';
}

function host(): string
{
    return (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function app_script_dir(): string
{
    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = str_replace('\\', '/', dirname($script));

    if ($dir === '' || $dir === '.' || $dir === '/') {
        return '';
    }

    return rtrim($dir, '/');
}

function mount_url_prefix(): string
{
    return app_script_dir();
}

function base_url(): string
{
    $override = config('app.url');

    if (is_string($override) && $override !== '') {
        return rtrim($override, '/');
    }

    return scheme() . '://' . host() . mount_url_prefix();
}

function url(string $path = ''): string
{
    if ($path === '' || preg_match('#^(?:[a-z][a-z0-9+.\-]*:)?//#i', $path)) {
        return $path === '' ? base_url() : $path;
    }

    if ($path === '/') {
        return base_url() . '/';
    }

    $path = '/' . ltrim($path, '/');
    $prefix = mount_url_prefix();

    if ($prefix !== '' && ($path === $prefix || str_starts_with($path, $prefix . '/'))) {
        return scheme() . '://' . host() . $path;
    }

    return base_url() . $path;
}
