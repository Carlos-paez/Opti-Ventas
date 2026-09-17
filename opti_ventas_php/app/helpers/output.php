<?php

declare(strict_types=1);

/**
 * Escape y generación de respuestas HTTP.
 */

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
