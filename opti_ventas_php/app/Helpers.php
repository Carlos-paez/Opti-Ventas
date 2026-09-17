<?php

declare(strict_types=1);

/* Path helpers */

function base_path(string $path = ''): string
{
    return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
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

/* Config */

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'];

    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }

    return $value;
}

/* Output */

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . url($to));
    exit;
}

function back(): never
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
    redirect($referer);
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function assert_failed(int $code, string $message = ''): never
{
    $reasons = [
        200 => 'OK',
        201 => 'Created',
        302 => 'Found',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        419 => 'Page Expired',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
    ];
    header('HTTP/1.1 ' . $code . ' ' . ($reasons[$code] ?? 'Error'), true, $code);
    echo e($message);
    exit;
}

/* URLs */

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
    $dir = app_script_dir();

    if ($dir === '/public') {
        return '';
    }

    if ($dir !== '' && preg_match('#^(.+)/public$#', $dir, $matches)) {
        return $matches[1];
    }

    return $dir;
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

/* Request helpers */

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
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';

    foreach ([app_script_dir(), mount_url_prefix()] as $prefix) {
        if ($prefix === '' || $prefix === '/') {
            continue;
        }

        if ($path === $prefix) {
            return '/';
        }

        if (str_starts_with($path, $prefix . '/')) {
            return rtrim(substr($path, strlen($prefix)), '/') ?: '/';
        }
    }

    return $path;
}

/* Session flash, errors, old input */

function flash(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function consume_flash(?string $key = null): mixed
{
    if ($key === null) {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flash;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $value;
}

function flash_errors(array $errors): void
{
    $_SESSION['errors'] = $errors;
}

function errors(): array
{
    return $_SESSION['errors'] ?? [];
}

function has_error(string $key): bool
{
    return isset($_SESSION['errors'][$key]);
}

function field_error(string $key): string
{
    return (string) ($_SESSION['errors'][$key] ?? '');
}

function old_input(array $data): void
{
    $_SESSION['old'] = $data;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old'][$key] ?? $default;
}

function clear_request_state(): void
{
    unset($_SESSION['errors'], $_SESSION['old']);
}

/* CSRF */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_token_valid(): bool
{
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

    return is_string($sent) && $sent !== '' && hash_equals(csrf_token(), $sent);
}

/* Misc */

function now(): string
{
    return date('Y-m-d H:i:s');
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ü' => 'u', 'ñ' => 'n', 'ç' => 'c',
    ]);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim((string) $text, '-');
}

function money_symbol(): string
{
    static $symbol = null;

    if ($symbol !== null) {
        return $symbol;
    }

    $currency = strtoupper(trim((string) (setting('currency', 'MXN') ?? 'MXN')));

    $symbol = match ($currency) {
        'USD', 'MXN', 'CAD', 'AUD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY', 'CNY' => '¥',
        default => $currency,
    };

    return $symbol;
}

function money(float $amount): string
{
    return money_symbol() . number_format($amount, 2);
}

function setting(string $key, mixed $default = null): mixed
{
    return \App\Models\Setting::get($key, $default);
}