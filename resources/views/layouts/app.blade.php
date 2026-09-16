<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Opti Ventas') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Opti Ventas</span>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    @php
                        $navItems = collect([
                            ['route' => 'dashboard', 'label' => 'Dashboard'],
                            ['route' => 'pos.index', 'label' => 'Punto de Venta'],
                            ['route' => 'products.index', 'label' => 'Productos'],
                            ['route' => 'categories.index', 'label' => 'Categorias'],
                            ['route' => 'customers.index', 'label' => 'Clientes'],
                            ['route' => 'sales.index', 'label' => 'Ventas'],
                            ['route' => 'reports.index', 'label' => 'Reportes'],
                        ]);

                        if (auth()->user()->is_admin) {
                            $navItems->push(['route' => 'users.index', 'label' => 'Usuarios']);
                            $navItems->push(['route' => 'settings.index', 'label' => 'Configuracion']);
                        }
                    @endphp

                    @foreach ($navItems as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                                  {{ $active
                                      ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400'
                                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if ($item['route'] === 'dashboard')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>
                                @elseif ($item['route'] === 'pos.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                @elseif ($item['route'] === 'products.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                @elseif ($item['route'] === 'categories.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                @elseif ($item['route'] === 'customers.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                @elseif ($item['route'] === 'sales.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                @elseif ($item['route'] === 'reports.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                @elseif ($item['route'] === 'users.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                @elseif ($item['route'] === 'settings.index')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                @endif
                            </svg>
                            {{ $item['label'] }}
                            @if ($item['route'] === 'pos.index')
                                <span class="ml-auto text-xs bg-primary-600 text-white px-1.5 py-0.5 rounded-full font-medium">POS</span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-gray-200 dark:border-gray-700 p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 font-semibold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
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
            <div class="sticky top-0 z-30 flex items-center h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 lg:hidden">
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

            @isset($header)
                <header class="hidden lg:block bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4">{{ $header }}</div>
                </header>
            @else
                @hasSection('header')
                    <header class="hidden lg:block bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4">@yield('header')</div>
                    </header>
                @endif
            @endisset

            <main class="p-4 lg:p-6">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>