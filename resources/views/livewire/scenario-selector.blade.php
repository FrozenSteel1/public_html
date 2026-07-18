<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Выберите сценарий</h1>

        <!-- Поиск -->
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Поиск сценариев..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>

        <!-- Список сценариев -->
        @if(count($scenarios) > 0)
            <div class="space-y-4">
                @foreach($scenarios as $scenario)
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <!-- Информация о сценарии -->
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold">{{ $scenario['name'] }}</h2>

                                <div class="mt-2 text-sm text-gray-600">
                                    {{ $scenario['description'] ?: 'Описание отсутствует' }}
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="text-xs px-2 py-1 bg-gray-100 rounded">
                                        Сцен: {{ $scenario['scenes_count'] }}
                                    </span>
                                    <span class="text-xs px-2 py-1 {{ $this->getDifficultyColor($scenario['difficulty']) }} rounded">
                                        {{ $this->getDifficultyLabel($scenario['difficulty']) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Выбор сложности и кнопка старта -->
                            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                                <!-- Выбор сложности -->
                                @php
                                    $availableDifficulties = $this->getAvailableDifficulties($scenario['id']);
                                @endphp

                                @if(count($availableDifficulties) > 0)
                                    <select
                                        wire:model="selectedDifficulties.{{ $scenario['id'] }}"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-auto"
                                    >
                                        @foreach($availableDifficulties as $difficulty)
                                            <option value="{{ $difficulty }}">
                                                {{ $this->getDifficultyLabel($difficulty) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-sm text-gray-500 px-3 py-2">
                                        Нет доступных сложностей
                                    </span>
                                @endif

                                <!-- Кнопка старта -->
                                <button
                                    wire:click="startGame({{ $scenario['id'] }})"
                                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition whitespace-nowrap w-full sm:w-auto"
                                >
                                    Начать игру
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Сценарии не найдены</p>
                @if(!empty($search))
                    <p class="text-sm text-gray-400 mt-2">Попробуйте изменить поисковый запрос</p>
                @endif
            </div>
        @endif
    </div>
</div>
