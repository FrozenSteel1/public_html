<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Стратегические шахматы: Госуправление' }}</title>
    @vite(['resources/css/app.css', 'resources/css/game.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div id="app">
    <header id="app-header">
        <div class="global-header__inner">
            <div class="global-header__branding">
                <span class="global-header__emblem" aria-hidden="true"></span>
                <div>
                    <p class="global-header__title">Стратегические шахматы: Госуправление</p>
                    <p class="global-header__subtitle">Симулятор государственного и муниципального управления</p>
                </div>
            </div>
            <nav class="global-header__utilities" aria-label="Служебные разделы">
                <ul>
                    <li>
                        <button type="button" class="global-header__action" title="Справка — раздел будет добавлен позже">
                            <x-game.icon name="book" /> Справка
                        </button>
                    </li>
                    <li>
                        <button type="button" class="global-header__action" title="Настройки — раздел будет добавлен позже">
                            <x-game.icon name="gear" /> Настройки
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="app-screen">
        {{ $slot }}
    </main>

    <div id="app-live-region" class="visually-hidden" aria-live="polite"></div>
</div>
@livewireScripts
@stack('scripts')
</body>
</html>
