<?php
/** @var string $__content */
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
        body.dark-mode .bg-gray-100,
        body.dark-mode .bg-gray-800,
        body.dark-mode .bg-gray-700 {
            background-color: #1f2937 !important;
        }

        body.dark-mode .text-gray-900,
        body.dark-mode .text-gray-800,
        body.dark-mode .text-gray-700,
        body.dark-mode .text-gray-600,
        body.dark-mode .text-gray-500,
        body.dark-mode .text-gray-400,
        body.dark-mode .text-gray-300 {
            color: #f3f4f6 !important;
        }

        body.dark-mode input,
        body.dark-mode textarea,
        body.dark-mode select,
        body.dark-mode button,
        body.dark-mode .rounded-lg,
        body.dark-mode .rounded-xl,
        body.dark-mode .rounded-md {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100 dark:bg-gray-900 dark:text-gray-100">
    <div class="absolute top-4 right-4 z-10">
        <button type="button" id="guestThemeToggle" aria-label="Cambiar tema" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors">
            <svg id="guestThemeToggleIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m12.95 6.95l-1.41-1.41M8.46 8.46L7.05 7.05m9.9 0l-1.41 1.41M8.46 15.54l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z"/>
            </svg>
        </button>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-600 text-white font-bold text-lg">OV</div>
            <span class="text-xl font-bold text-gray-900 dark:text-white"><?= e((string) setting('business_name', config('app.name', 'Opti Ventas'))) ?></span>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <?php include views_path('partials/flash.php'); ?>
            <?php include views_path('partials/errors.php'); ?>
            <?= $__content ?>
        </div>
    </div>

    <script>
        $(function () {
            const $guestThemeToggle = $('#guestThemeToggle');
            const $guestThemeToggleIcon = $('#guestThemeToggleIcon');

            function applyGuestTheme(isDark) {
                $('html, body').toggleClass('dark', isDark);
                $('body').toggleClass('dark-mode', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                const iconPath = isDark
                    ? 'M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z'
                    : 'M12 3v2m0 14v2m9-9h-2M5 12H3m12.95 6.95l-1.41-1.41M8.46 8.46L7.05 7.05m9.9 0l-1.41 1.41M8.46 15.54l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z';
                $guestThemeToggleIcon.attr('d', iconPath);
            }

            const savedGuestTheme = localStorage.getItem('theme');
            const prefersGuestDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyGuestTheme(savedGuestTheme ? savedGuestTheme === 'dark' : prefersGuestDark);

            $guestThemeToggle.on('click', function () {
                const isDark = !$('html').hasClass('dark');
                applyGuestTheme(isDark);
            });
        });
    </script>
</body>
</html>