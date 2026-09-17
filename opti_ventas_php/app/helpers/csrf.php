<?php

declare(strict_types=1);

/**
 * Protección CSRF.
 */

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
    $sent = $_POST['_csrf'] ?? null;

    if ($sent === null && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $key => $val) {
            if (strcasecmp((string) $key, 'X-CSRF-TOKEN') === 0 || strcasecmp((string) $key, 'X-XSRF-TOKEN') === 0) {
                $sent = $val;
                break;
            }
        }
    }

    if ($sent === null && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $sent = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }

    if ($sent === null) {
        $json = json_input();
        if (isset($json['_csrf']) && is_string($json['_csrf'])) {
            $sent = $json['_csrf'];
        }
    }

    return is_string($sent) && $sent !== '' && hash_equals(csrf_token(), $sent);
}
