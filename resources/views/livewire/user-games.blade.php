<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Мои игры</h1>
        <div class="flex gap-3">
            <button
                wire:click="toggleShowAll"
                class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-lg transition"
            >
                {{ $showAll ? 'Показать активные' : 'Показать все' }}
            </button>
            <a href="{{ route('scenarios') }}"
               class="px-4 py-2 text-sm bg-blue-500 text-white hover:bg-blue-600 rounded-lg transition">
                + Новая игра
            </a>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(count($games) > 0)
        <div class="space-y-3">
            @foreach($games as $game)
                <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg">{{ $game['scenario_name'] }}</h3>
                                @if($game['is_finished'])
                                    <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">Завершена</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-600 rounded-full">В процессе</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                <span>Сцена: {{ $game['current_scene_title'] }}</span>
                                <span class="mx-2">•</span>
                                <span>Сложность: {{ $game['difficulty'] }}</span>
                                <span class="mx-2">•</span>
                                <span>Шагов: {{ $game['steps_count'] }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $game['created_at'] }}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if(!$game['is_finished'])
                                <button
                                    wire:click="continueGame({{ $game['id'] }})"
                                    class="px-4 py-2 text-sm bg-blue-500 text-white hover:bg-blue-600 rounded-lg transition"
                                >
                                    Продолжить
                                </button>
                            @endif
                            <button
                                wire:click="deleteGame({{ $game['id'] }})"
                                onclick="return confirm('Удалить эту игру?')"
                                class="px-3 py-2 text-sm bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition"
                            >
                                🗑
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-gray-500 text-lg">У вас пока нет игр</p>
            <a href="{{ route('scenarios') }}" class="inline-block mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Начать первую игру
            </a>
        </div>
    @endif
</div>
