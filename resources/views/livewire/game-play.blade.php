<div wire:key="game-{{ $game->id }}-{{ $renderKey }}" class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Основная область (2/3 ширины) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Шапка сцены -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-blue-100 text-sm font-medium">Сцена {{ $scene->order ?? 0 }}</span>
                            <h2 class="text-xl font-bold text-white">{{ $scene->title ?? 'Без названия' }}</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                                {{ $game->difficulty }}
                            </span>
                            <button
                                wire:click="forceNewGame"
                                class="px-3 py-1 text-xs bg-red-500/20 hover:bg-red-500/30 text-white rounded-lg transition"
                                onclick="return confirm('Начать новую игру? Текущий прогресс будет потерян.')"
                            >
                                🔄 Новая игра
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Содержание -->
                <div class="p-6">
                    <!-- Ситуация -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $scene->situation ?? 'Описание отсутствует' }}
                    </div>

                    <!-- Дополнительные данные (компактно) -->
                    @php
                        $additional = $this->getAdditionalData();
                    @endphp
                    @if(!empty($additional))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            @foreach($additional as $item)
                                @if(is_array($item) && isset($item['key']) && isset($item['value']))
                                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                        <div class="text-xs font-semibold text-blue-600 mb-1">{{ $item['key'] }}</div>
                                        <div class="text-sm text-gray-700 line-clamp-3">{{ $item['value'] }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Выборы -->
                    @if(count($availableChoices) > 0)
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Выберите действие:</h3>
                            @foreach($availableChoices as $choice)
                                <button
                                    wire:key="choice-{{ $choice->id }}-{{ $game->id }}"
                                    wire:click="selectChoice({{ $choice->id }})"
                                    class="w-full text-left px-4 py-2.5 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-lg transition text-sm text-gray-700 hover:text-blue-700"
                                >
                                    {{ $choice->description }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Нет доступных выборов.</p>
                    @endif

                    <!-- Реакции акторов (только сообщения) -->
                    @if(count($triggeredEvents) > 0)
                        <div class="mt-4 space-y-2">
                            @foreach($triggeredEvents as $event)
                                @if(!empty($event['messages']))
                                    @foreach($event['messages'] as $message)
                                        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg p-3 text-sm text-gray-700">
                                            {{ $message }}
                                        </div>
                                    @endforeach
                                @elseif(!empty($event['event_description']))
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg p-3 text-sm text-gray-700">
                                        {{ $event['event_description'] }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Сообщения -->
                    @if(session()->has('message'))
                        <div class="mt-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2 rounded-lg">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="mt-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Боковая панель (1/3 ширины) -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Только параметры -->
            <div class="bg-white rounded-xl shadow-md p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">📊 Параметры</h3>
                @if(!empty($currentState))
                    <div class="space-y-2.5">
                        @foreach($currentState as $key => $value)
                            <div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">{{ $this->getParameterLabel($key) }}</span>
                                    <span class="font-medium">{{ $value }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-0.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                         style="width: {{ $value }}%; background-color: {{ $this->getParameterColor($value) == 'red' ? '#ef4444' : ($this->getParameterColor($value) == 'orange' ? '#f59e0b' : ($this->getParameterColor($value) == 'yellow' ? '#eab308' : ($this->getParameterColor($value) == 'blue' ? '#3b82f6' : '#22c55e'))) }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Нет данных</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('game-updated', () => {
                // Принудительно обновляем компонент
                Livewire.dispatch('refresh');
            });
        });
    </script>
@endpush
