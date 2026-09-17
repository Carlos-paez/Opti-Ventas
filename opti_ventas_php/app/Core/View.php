<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static array $sections = [];
    public static array $sectionStack = [];

    public static function render(string $view, array $data = [], ?string $layout = 'app'): void
    {
        self::$sections = [];
        self::$sectionStack = [];

        extract($data, EXTR_SKIP);

        ob_start();
        include views_path($view . '.php');
        $__content = ob_get_clean();

        if ($layout === null) {
            echo $__content;

            return;
        }

        include views_path('layouts' . DIRECTORY_SEPARATOR . $layout . '.php');
    }

    public static function startSection(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function endSection(): void
    {
        $content = ob_get_clean();
        $name = array_pop(self::$sectionStack);

        if ($name !== null) {
            self::$sections[$name] = $content;
        }
    }

    public static function hasSection(string $name): bool
    {
        return !empty(self::$sections[$name] ?? null);
    }

    public static function yieldSection(string $name): void
    {
        echo self::$sections[$name] ?? '';
    }

    public static function pushScript(string $script): void
    {
        self::$sections['scripts'] = (self::$sections['scripts'] ?? '') . $script;
    }
}