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
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-600 text-white font-bold text-lg">OV</div>
            <span class="text-xl font-bold text-gray-900"><?= e((string) setting('business_name', config('app.name', 'Opti Ventas'))) ?></span>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <?php include views_path('partials/flash.php'); ?>
            <?php include views_path('partials/errors.php'); ?>
            <?= $__content ?>
        </div>
    </div>
</body>
</html>