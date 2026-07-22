<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Выберите сценарий</h1>

        <!-- Сообщение об активной игре -->
        @if($activeGame && !$showModal)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎮</span>
                    <div>
                        <p class="text-sm text-gray-700">
                            У вас есть активная игра: <strong>{{ $activeGame['scenario_name'] }}</strong>
                        </p>
                        <p class="text-xs text-gray-500">
                            Сцена: {{ $activeGame['current_scene_title'] }} • Шагов: {{ $activeGame['steps'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('game.continue', ['gameId' => $activeGame['id']]) }}"
                   class="px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition whitespace-nowrap">
                    Продолжить
                </a>
            </div>
        @endif

        <!-- Поиск -->
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Поиск сценариев..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
            >
        </div>

        <!-- Список сценариев -->
        @if(count($scenarios) > 0)
            <div class="space-y-4">
                @foreach($scenarios as $scenario)
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <!-- Информация о сценарии (на всю ширину) -->
                        <div class="w-full">
                            <h2 class="text-xl font-semibold text-gray-800">{{ $scenario['name'] }}</h2>

                            <div class="mt-2 text-sm text-gray-600 leading-relaxed break-words">
                                {{ $scenario['description'] ?: 'Описание отсутствует' }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full">
                                    📄 Сцен: {{ $scenario['scenes_count'] }}
                                </span>
                                <span class="text-xs px-2.5 py-1 {{ $this->getDifficultyColor($scenario['difficulty']) }} rounded-full">
                                    {{ $this->getDifficultyLabel($scenario['difficulty']) }}
                                </span>
                            </div>
                        </div>

                        <!-- Управление (на всю ширину, с переносом) -->
                        <div class="w-full mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-3">
                            @php
                                $availableDifficulties = $this->getAvailableDifficulties($scenario['id']);
                            @endphp

                            @if(count($availableDifficulties) > 0)
                                <select
                                    wire:model="selectedDifficulties.{{ $scenario['id'] }}"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                >
                                    @foreach($availableDifficulties as $difficulty)
                                        <option value="{{ $difficulty }}">
                                            {{ $this->getDifficultyLabel($difficulty) }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            <button
                                wire:click="tryStartGame({{ $scenario['id'] }})"
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium"
                            >
                                Начать игру →
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-lg">Сценарии не найдены</p>
                @if(!empty($search))
                    <p class="text-sm text-gray-400 mt-2">Попробуйте изменить поисковый запрос</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Модальное окно -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closeModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                <h2 class="text-xl font-bold mb-2">У вас есть активная игра</h2>
                <p class="text-gray-600 text-sm mb-4">
                    Вы уже начали прохождение сценария <strong>{{ $activeGame['scenario_name'] ?? 'неизвестного' }}</strong>
                </p>

                <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Сцена:</span>
                        <span>{{ $activeGame['current_scene_title'] ?? 'Продолжить' }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-600">Сложность:</span>
                        <span>{{ $this->getDifficultyLabel($activeGame['difficulty'] ?? 'easy') }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-600">Сделано шагов:</span>
                        <span>{{ $activeGame['steps'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-600">Начата:</span>
                        <span>{{ $activeGame['created_at'] ?? '' }}</span>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mb-4">
                    Что вы хотите сделать?
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        wire:click="continueActiveGame"
                        class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                    >
                        Продолжить игру
                    </button>
                    <button
                        wire:click="startNewGame({{ $pendingScenarioId }})"
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                    >
                        Начать новую
                    </button>
                    <button
                        wire:click="closeModal"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
