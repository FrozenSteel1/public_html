<div>
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Заголовок -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Мои игры</h1>
                <p class="text-gray-500 text-sm">Добро пожаловать, {{ auth()->user()->name }}!</p>
            </div>
            <button
                wire:click="startNewGame"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
                + Новая игра
            </button>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_games'] }}</div>
                <div class="text-xs text-gray-500">Всего игр</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $statistics['completed_games'] }}</div>
                <div class="text-xs text-gray-500">Завершено</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $statistics['in_progress_games'] }}</div>
                <div class="text-xs text-gray-500">В процессе</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $statistics['failed_games'] }}</div>
                <div class="text-xs text-gray-500">Провалено</div>
            </div>
        </div>

        <!-- Активная игра -->
        @if($hasActiveGame && $activeGame)
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 mb-6 border border-blue-200">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-200 px-2 py-0.5 rounded-full">Активная игра</span>
                        <h3 class="text-lg font-bold text-gray-800 mt-1">{{ $activeGame->currentScene->title ?? 'Игра в процессе' }}</h3>
                        <p class="text-sm text-gray-600">Сценарий: {{ $this->getScenarioName($activeGame) }}</p>
                    </div>
                    <button
                        wire:click="continueGame({{ $activeGame->id }})"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                    >
                        Продолжить
                    </button>
                </div>
            </div>
        @endif

        <!-- Список игр -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-700">История игр</h2>
            </div>

            @if(count($games) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($games as $game)
                        <div class="px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-gray-50 transition">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-800 text-sm">{{ $game['scenario_name'] }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        {{ $game['difficulty'] }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @if($game['status_color'] === 'green') bg-green-100 text-green-700
                                        @elseif($game['status_color'] === 'yellow') bg-yellow-100 text-yellow-700
                                        @elseif($game['status_color'] === 'red') bg-red-100 text-red-700
                                        @else bg-blue-100 text-blue-700 @endif">
                                        {{ $game['status_label'] }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $game['created_at'] }} • {{ $game['current_scene_title'] }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if($game['is_active'])
                                    <button
                                        wire:click="continueGame({{ $game['id'] }})"
                                        class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                                    >
                                        Продолжить
                                    </button>
                                @else
                                    <button
                                        wire:click="viewResults({{ $game['id'] }})"
                                        class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition"
                                    >
                                        Результаты
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <div class="text-4xl mb-2">🎮</div>
                    <p class="text-gray-500 text-sm">У вас пока нет игр</p>
                    <button
                        wire:click="startNewGame"
                        class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm"
                    >
                        Начать первую игру
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
