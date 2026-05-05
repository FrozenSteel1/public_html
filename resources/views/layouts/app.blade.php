<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    <!-- Навигация -->
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Логотип и основные ссылки -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold">🎮 Game Admin</a>

                    <a href="{{ route('admin.companies') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.companies') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        🏢 Компании
                    </a>
                    <a href="{{ route('admin.scenarios') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.scenarios') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        📋 Сценарии
                    </a>
                    <a href="{{ route('admin.scenes') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.scenes') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        🎬 Сцены
                    </a>
                    <a href="{{ route('admin.events') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.events') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        ⚡ События
                    </a>
                    <a href="{{ route('admin.effect-types') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.effect-types') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        🎯 Типы эффектов
                    </a>
                    <a href="{{ route('admin.actors') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.actors') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        👤 Акторы
                    </a>
                    <a href="{{ route('admin.company-scenarios') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.company-scenarios') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        🔗 Связи
                    </a>
                    <a href="{{ route('admin.presets') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.presets') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        💾 Предустановки
                    </a>
                    <a href="{{ route('admin.games') }}"
                       class="px-3 py-2 rounded text-sm {{ request()->routeIs('admin.games') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        🎮 Игры
                    </a>
                </div>

                <!-- Пользовательское меню -->
                <div class="flex items-center">
                    <span class="text-sm mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-300 hover:text-white">
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Контент -->
    <main>
        {{ $slot }}
    </main>
</div>

@livewireScripts
</body>
</html>
