<div>
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Основная область (2/3 ширины) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Шапка сцены -->
                    <div wire:key="header-{{ $game->id }}-{{ $scene->id ?? 0 }}" class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-blue-100 text-sm font-medium">Сцена {{ $scene->order ?? 0 }}</span>
                                <h2 class="text-xl font-bold text-white">{{ $scene->title ?? 'Без названия' }}</h2>
                            </div>
                            <div class="flex items-center gap-2">
                                <!-- Таймер с AlpineJS -->
                                <div
                                    x-data="{
                                        timeLeft: {{ $timeLimit }},
                                        timer: null,
                                        isExpired: false,
                                        init() {
                                            this.timeLeft = {{ $timeLimit }};
                                            this.startTimer();
                                        },
                                        startTimer() {
                                            if (this.timer) clearInterval(this.timer);
                                            this.timer = setInterval(() => {
                                                this.timeLeft--;
                                                if (this.timeLeft <= 0) {
                                                    this.timeLeft = 0;
                                                    this.isExpired = true;
                                                    clearInterval(this.timer);
                                                    $wire.timeExpired();
                                                }
                                            }, 1000);
                                        },
                                        restart(seconds) {
                                            this.timeLeft = seconds;
                                            this.isExpired = false;
                                            if (this.timer) {
                                                clearInterval(this.timer);
                                                this.timer = null;
                                            }
                                            this.startTimer();
                                        },
                                        formatTime() {
                                            const minutes = Math.floor(this.timeLeft / 60);
                                            const seconds = this.timeLeft % 60;
                                            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
                                        }
                                    }"
                                    x-init="init()"
                                    @timer-restart.window="restart($event.detail.seconds)"
                                    id="game-timer-container"
                                    class="px-3 py-1 text-white text-xs font-semibold rounded-full flex items-center gap-1.5 min-w-[60px] justify-center"
                                    :class="{
                                        'bg-green-500/30': timeLeft > 30,
                                        'bg-yellow-500/30': timeLeft <= 30 && timeLeft > 10,
                                        'bg-orange-500/40': timeLeft <= 10 && timeLeft > 0,
                                        'bg-red-500/50 animate-pulse': timeLeft <= 0
                                    }"
                                >
                                    <span>⏱</span>
                                    <span x-text="formatTime()"></span>
                                </div>

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
                    <div wire:key="content-{{ $game->id }}-{{ $scene->id ?? 0 }}" class="p-6">
                        <!-- Ситуация -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $scene->situation ?? 'Описание отсутствует' }}
                        </div>

                        <!-- Дополнительные данные -->
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
                            <div wire:key="choices-{{ $game->id }}-{{ $scene->id ?? 0 }}" class="space-y-2">
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
                <!-- Акторы -->
                <div wire:key="actors-{{ $game->id }}-{{ $scene->id ?? 0 }}" class="bg-white rounded-xl shadow-md p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">🎭 Акторы</h3>
                    @if(count($sceneActors) > 0)
                        <div class="space-y-2">
                            @foreach($sceneActors as $actor)
                                <div class="actor-tooltip-trigger">
                                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                                        <span class="text-lg">👤</span>
                                        <span class="text-sm font-medium text-gray-700">{{ $actor['name'] }}</span>
                                    </div>
                                    <!-- Тултип -->
                                    <div class="actor-tooltip">
                                        <div class="tooltip-title">{{ $actor['name'] }}</div>
                                        <div class="tooltip-description">{{ $actor['description'] ?: 'Описание отсутствует' }}</div>
                                        @php
                                            $settings = is_array($actor['settings']) ? $actor['settings'] : [];
                                        @endphp
                                        @if(!empty($settings))
                                            <div class="tooltip-settings">
                                                @foreach($settings as $setting)
                                                    @if(is_array($setting) && isset($setting['key']) && isset($setting['value']))
                                                        <span class="tooltip-setting">
                                                            {{ $setting['key'] }}: {{ $setting['value'] }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Нет акторов в этой сцене</p>
                    @endif
                </div>

                <!-- История событий -->
                <div wire:key="history-{{ $game->id }}" class="bg-white rounded-xl shadow-md p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📜 История</h3>
                    @if(count($gameHistoryWithMonths) > 0)
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach($gameHistoryWithMonths as $event)
                                <div class="flex items-start gap-2 py-1.5 border-b border-gray-50 text-xs">
                                    <span class="text-blue-600 font-medium whitespace-nowrap min-w-[70px]">
                                        {{ $event['month'] }}
                                    </span>
                                    <span class="text-gray-700 truncate">
                                        @if($event['is_actor_event'] ?? false)
                                            <span class="text-gray-400">↳</span>
                                            <span class="text-gray-500">{{ $event['actor_name'] ?? 'Актор' }}</span>
                                            <span class="text-gray-400">—</span>
                                            <span class="text-gray-600">{{ $event['actor_type'] ?? $event['event_name'] }}</span>
                                        @else
                                            {{ $event['event_name'] }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 text-xs text-gray-400 text-center">
                            Всего событий: {{ count($gameHistoryWithMonths) }}
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">История пуста</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно с сообщением -->
    @if($showMessageModal && !empty($currentModalMessage))
        <div wire:key="modal-{{ $game->id }}" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; border-radius: 12px; max-width: 400px; width: 90%; margin: 0 auto; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div style="text-align: center;">
                    <div style="margin-bottom: 16px;">
                        <div style="width: 64px; height: 64px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <span style="font-size: 30px;">ℹ️</span>
                        </div>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">Сообщение</h3>
                    <p style="color: #4b5563; margin-bottom: 20px; font-size: 16px; line-height: 1.5;">
                        {{ $currentModalMessage['text'] }}
                    </p>
                    <button
                        wire:click="closeMessageModal"
                        style="padding: 10px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;"
                    >
                        Продолжить
                    </button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Слушаем события отладки
                Livewire.on('console-log', (data) => {
                    const logData = data[0] || data;

                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');
                    console.log('%c' + logData.title, 'color: #2196F3; font-size: 14px; font-weight: bold;');
                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');

                    Object.keys(logData).forEach(key => {
                        if (key === 'type' || key === 'title') return;

                        console.group(`%c📌 ${key}`, 'color: #FF5722; font-weight: bold;');

                        if (typeof logData[key] === 'object') {
                            console.log(JSON.stringify(logData[key], null, 2));
                        } else {
                            console.log(logData[key]);
                        }
                        console.groupEnd();
                    });

                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');
                    console.log('');
                });
            });
        </script>
    @endpush
</div>
