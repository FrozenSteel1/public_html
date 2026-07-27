<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

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
                    <a href="{{ route('player.dashboard') }}" class="text-xl font-bold">🎮 Game</a>

                    @auth
                        <!-- Ссылки для всех авторизованных -->
                        <a href="{{ route('player.dashboard') }}"
                           class="px-3 py-2 rounded text-sm {{ request()->routeIs('player.dashboard') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                            📊 Мои игры
                        </a>
                        <a href="{{ route('scenarios') }}"
                           class="px-3 py-2 rounded text-sm {{ request()->routeIs('scenarios') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                            🎯 Сценарии
                        </a>

                        <!-- Админ-панель - только для админов и если маршрут существует -->
                        @if(auth()->user()->isAdmin() && Route::has('admin.dashboard'))
                            <span class="text-gray-500">|</span>
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-2 rounded text-sm bg-blue-600 hover:bg-blue-700 {{ request()->routeIs('admin.*') ? 'bg-blue-700' : '' }}">
                                ⚙️ Админ-панель
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Пользовательское меню -->
                <div class="flex items-center">
                    @auth
                        <span class="text-sm mr-4">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-300 hover:text-white">
                                Выйти
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white mr-4">Войти</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-300 hover:text-white">Регистрация</a>
                    @endauth
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
@stack('scripts')
</body>
</html>
