<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function add(array $methods, string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function get(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add(['GET'], $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add(['POST', 'PUT', 'PATCH', 'DELETE'], $pattern, $handler, $middleware);
    }

    public function middle($group, callable $register): void
    {
        // Kept for clarity; groups are expressed per-route, so nothing to do here.
        $register();
    }

    public function dispatch(string $uri, string $method): void
    {
        if (preg_match('#^/storage/(.+)$#', $uri, $match)) {
            self::serveFile($match[1]);

            return;
        }

        $method = request_method();

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            $pattern = '#^' . str_replace('{id}', '([0-9]+)', $route['pattern']) . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            if (in_array('auth', $route['middleware'], true) && !Auth::check()) {
                redirect('/login');
            }

            if (in_array('admin', $route['middleware'], true) && !Auth::isAdmin()) {
                assert_failed(403, 'No tienes permisos para realizar esta acción.');
            }

            if ($method !== 'GET' && !csrf_token_valid()) {
                assert_failed(419, 'El token CSRF es inválido o ha expirado.');
            }

            array_shift($matches);

            $handler = $route['handler'];
            $args = array_map(
                static fn ($arg): int|string => is_string($arg) && ctype_digit($arg) ? (int) $arg : $arg,
                array_values($matches)
            );

            if (is_callable($handler)) {
                $handler(...$args);

                return;
            }

            [$class, $action] = $handler;
            (new $class())->{$action}(...$args);

            return;
        }

        assert_failed(404, 'Página no encontrada.');
    }

    private static function serveFile(string $relative): void
    {
        $relative = ltrim($relative, '/');
        $path = realpath(storage_path($relative));

        if ($path === false || !str_starts_with($path, realpath(storage_path()))) {
            assert_failed(404, 'Archivo no encontrado.');
        }

        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
        ];

        header('Content-Type: ' . ($mimes[$extension] ?? 'application/octet-stream'));
        header('Content-Length: ' . (string) filesize($path));
        readfile($path);
        exit;
    }
}