<div>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Шапка -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8 text-center">
                <div class="text-4xl mb-2">🏆</div>
                <h1 class="text-2xl font-bold text-white">Игра завершена!</h1>
                <p class="text-green-100 mt-1">{{ $scenarioName }}</p>
            </div>

            <!-- Статистика -->
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalSteps }}</div>
                        <div class="text-xs text-gray-500">Сделано шагов</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ ucfirst($difficulty) }}</div>
                        <div class="text-xs text-gray-500">Сложность</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ count($finalState) }}</div>
                        <div class="text-xs text-gray-500">Параметров</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['finished_at'] }}</div>
                        <div class="text-xs text-gray-500">Завершена</div>
                    </div>
                </div>

                <!-- Финальные параметры -->
                <h3 class="font-semibold text-gray-700 mb-3">📊 Параметры</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                    @foreach($parametersWithDiff as $key => $data)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $key }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400 line-through">{{ $data['initial'] }}</span>
                                    <span class="text-sm font-bold text-blue-600">{{ $data['final'] }}</span>
                                    <span class="text-xs font-semibold {{ $data['diff_color'] }}">
                                        ({{ $data['diff_text'] }})
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="h-1.5 rounded-full transition-all duration-300"
                                     style="width: {{ $data['final'] }}%; background-color:
                                        {{ $data['final'] >= 80 ? '#22c55e' :
                                           ($data['final'] >= 60 ? '#3b82f6' :
                                           ($data['final'] >= 40 ? '#eab308' :
                                           ($data['final'] >= 20 ? '#f59e0b' : '#ef4444'))) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- История -->
                <h3 class="font-semibold text-gray-700 mb-3">📜 История ходов</h3>
                <div class="space-y-4 mb-6">
                    @forelse($historyData as $step)
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-bold text-blue-600">{{ $step['month'] }}</span>
                                    <span class="text-gray-400 mx-2">|</span>
                                    <span class="text-sm text-gray-500">Сцена {{ $step['scene_order'] }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $step['date'] }}</span>
                            </div>

                            <div class="text-sm font-medium text-gray-700 mb-1">
                                {{ $step['scene_title'] }}
                            </div>

                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Выбор игрока:</span>
                                {{ $step['choice_description'] }}
                            </div>

                            @if(!empty($step['effects']))
                                <div class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium">Эффект:</span>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($step['effects'] as $effect)
                                            <span
                                                class="text-xs px-2 py-1 rounded-full {{ $effect['is_positive'] ? 'bg-green-100 text-green-700' : ($effect['is_negative'] ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $effect['key'] }} {{ $effect['display'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($step['actor_reactions']))
                                <div class="text-sm text-gray-600 mt-2">
                                    <span class="font-medium">Реакции:</span>
                                    <span class="text-gray-500">
                                        @foreach($step['actor_reactions'] as $index => $reaction)
                                            {{ $reaction['actor'] }} — {{ $reaction['event'] }}@if(!$loop->last), @endif
                                        @endforeach
                                    </span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">История пуста</p>
                    @endforelse
                </div>

                <!-- Кнопки -->
                <div class="mt-6 flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('scenarios') }}"
                       class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        🎮 Новый сценарий
                    </a>
                    <a href="{{ route('user.games') }}"
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        📋 Мои игры
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
