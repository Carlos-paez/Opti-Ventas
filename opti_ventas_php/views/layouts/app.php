<?php
/** @var string $__content */
use App\Core\Auth;
use App\Core\View;

$user = Auth::user();
$isAdmin = Auth::isAdmin();
$path = request_path();

$navItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
    ['key' => 'pos', 'label' => 'Punto de Venta', 'icon' => 'pos', 'badge' => 'POS'],
    ['key' => 'products', 'label' => 'Productos', 'icon' => 'products'],
    ['key' => 'categories', 'label' => 'Categorias', 'icon' => 'categories'],
    ['key' => 'customers', 'label' => 'Clientes', 'icon' => 'customers'],
    ['key' => 'sales', 'label' => 'Ventas', 'icon' => 'sales'],
    ['key' => 'reports', 'label' => 'Reportes', 'icon' => 'reports'],
];

if ($isAdmin) {
    $navItems[] = ['key' => 'users', 'label' => 'Usuarios', 'icon' => 'users'];
    $navItems[] = ['key' => 'settings', 'label' => 'Configuracion', 'icon' => 'settings'];
}

function navRoute(string $key): string
{
    return match ($key) {
        'dashboard' => '/dashboard',
        'pos' => '/pos',
        'products' => '/products',
        'categories' => '/categories',
        'customers' => '/customers',
        'sales' => '/sales',
        'reports' => '/reports',
        'users' => '/users',
        'settings' => '/settings',
        default => '/dashboard',
    };
}

function navActive(string $key, string $path): bool
{
    $route = navRoute($key);
    $route = $route === '/dashboard' ? '/dashboard' : $route;

    return $path === $route || str_starts_with($path, $route . '/');
}

function navIcon(string $key): string
{
    return match ($key) {
        'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4',
        'pos' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'categories' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'customers' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'sales' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'reports' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        default => '',
    };
}
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e((string) setting('business_name', config('app.name', 'Opti Ventas'))) ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8',
                            500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        html { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        body {
            background-color: #f3f4f6;
            color: #111827;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        body.dark-mode {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
        }

        body.dark-mode .bg-white,
        body.dark-mode .bg-gray-50,
        body.dark-mode .bg-gray-100,
        body.dark-mode .bg-gray-200,
        body.dark-mode .bg-gray-800,
        body.dark-mode .bg-gray-700,
        body.dark-mode .bg-primary-50,
        body.dark-mode .bg-primary-100,
        body.dark-mode .bg-indigo-50 {
            background-color: #1f2937 !important;
        }

        body.dark-mode .text-gray-900,
        body.dark-mode .text-gray-800,
        body.dark-mode .text-gray-700,
        body.dark-mode .text-gray-600,
        body.dark-mode .text-gray-500,
        body.dark-mode .text-gray-400,
        body.dark-mode .text-gray-300,
        body.dark-mode .text-gray-200,
        body.dark-mode .text-gray-100 {
            color: #f3f4f6 !important;
        }

        body.dark-mode .border-gray-200,
        body.dark-mode .border-gray-300,
        body.dark-mode .border-gray-600,
        body.dark-mode .border-gray-700,
        body.dark-mode .border-indigo-200 {
            border-color: #374151 !important;
        }

        body.dark-mode input,
        body.dark-mode textarea,
        body.dark-mode select,
        body.dark-mode .rounded-lg,
        body.dark-mode .rounded-xl,
        body.dark-mode .rounded-md {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }

        body.dark-mode button:not(.bg-indigo-600):not(.bg-primary-600):not(.bg-primary-700):not(.bg-primary-500) {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }

        body.dark-mode .shadow-sm,
        body.dark-mode .shadow-md,
        body.dark-mode .shadow-lg,
        body.dark-mode .shadow-xl {
            box-shadow: none !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="h-full font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-full">
        <div id="sidebar-overlay"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"
             onclick="toggleSidebar()"></div>

        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
            <div class="flex flex-col h-full">
                <div class="flex items-center gap-2 h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-600 text-white font-bold text-sm">OV</div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white"><?= e((string) setting('business_name', config('app.name', 'Opti Ventas'))) ?></span>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <?php foreach ($navItems as $item): ?>
                        <?php $active = navActive($item['key'], $path); ?>
                        <a href="<?= e(url(navRoute($item['key']))) ?>"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= $active ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e(navIcon($item['key'])) ?>"/>
                            </svg>
                            <?= e($item['label']) ?>
                            <?php if (!empty($item['badge'])): ?>
                                <span class="ml-auto text-xs bg-primary-600 text-white px-1.5 py-0.5 rounded-full font-medium"><?= e($item['badge']) ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="border-t border-gray-200 dark:border-gray-700 p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 font-semibold text-sm">
                            <?= e(mb_strtoupper(mb_substr((string) ($user['name'] ?? 'U'), 0, 1))) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= e((string) ($user['name'] ?? '')) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= e(ucfirst((string) ($user['role'] ?? ''))) ?></p>
                        </div>
                        <button type="button" id="themeToggle" aria-label="Cambiar tema" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg id="themeToggleIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m12.95 6.95l-1.41-1.41M8.46 8.46L7.05 7.05m9.9 0l-1.41 1.41M8.46 15.54l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                            </svg>
                        </button>
                        <form method="POST" action="<?= url('/logout') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="lg:pl-64">
            <div class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 lg:hidden">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2 ml-3">
                        <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-primary-600 text-white font-bold text-xs">OV</div>
                        <span class="font-bold text-gray-900 dark:text-white">Opti Ventas</span>
                    </div>
                </div>

                <button type="button" id="mobileThemeToggle" aria-label="Cambiar tema" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m12.95 6.95l-1.41-1.41M8.46 8.46L7.05 7.05m9.9 0l-1.41 1.41M8.46 15.54l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                    </svg>
                </button>
            </div>

            <?php if (View::hasSection('header')): ?>
                <header class="hidden lg:block bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4"><?php View::yieldSection('header'); ?></div>
                </header>
            <?php endif; ?>

            <main class="p-4 lg:p-6">
                <?php include views_path('partials/flash.php'); ?>
                <?php include views_path('partials/errors.php'); ?>
                <?= $__content ?>
            </main>
        </div>
    </div>

    <script>
        $(function () {
            const $themeToggleButtons = $('#themeToggle, #mobileThemeToggle');

            function applyTheme(isDark) {
                $('html, body').toggleClass('dark', isDark);
                $('body').toggleClass('dark-mode', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                const iconPaths = {
                    dark: 'M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z',
                    light: 'M12 3v2m0 14v2m9-9h-2M5 12H3m12.95 6.95l-1.41-1.41M8.46 8.46L7.05 7.05m9.9 0l-1.41 1.41M8.46 15.54l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z',
                };

                $themeToggleButtons.each(function () {
                    const $icon = $(this).find('svg');
                    if ($icon.length) {
                        $icon.attr('d', isDark ? iconPaths.dark : iconPaths.light);
                    }
                });
            }

            window.toggleSidebar = function () {
                $('#sidebar').toggleClass('-translate-x-full');
                $('#sidebar-overlay').toggleClass('hidden');
            };

            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(savedTheme ? savedTheme === 'dark' : prefersDark);

            $themeToggleButtons.on('click', function () {
                const isDark = !$('html').hasClass('dark');
                applyTheme(isDark);
            });
        });
    </script>

    <?php View::yieldSection('scripts'); ?>
</body>
</html>