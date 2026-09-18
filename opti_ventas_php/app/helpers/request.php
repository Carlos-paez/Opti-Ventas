<?php

declare(strict_types=1);

/**
 * Lectura de la petición HTTP entrante.
 */

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function request_method(): string
{
    return is_post() && isset($_POST['_method'])
        ? strtoupper((string) $_POST['_method'])
        : ($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function input(?string $key = null, mixed $default = null): mixed
{
    $data = array_merge($_GET, $_POST);

    if ($key === null) {
        return $data;
    }

    return $data[$key] ?? $default;
}

function query(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw === false ? '' : $raw, true);

    return is_array($data) ? $data : [];
}

function request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? $_SERVER['ORIG_PATH_INFO'] ?? $_SERVER['PATH_INFO'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';

    $prefix = mount_url_prefix();

    if ($prefix === '') {
        return $path;
    }

    if ($path === $prefix) {
        return '/';
    }

    if (str_starts_with($path, $prefix . '/')) {
        return rtrim(substr($path, strlen($prefix)), '/') ?: '/';
    }

    return $path;
}
