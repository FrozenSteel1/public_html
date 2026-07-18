<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Scene;
use App\Models\Scenario;
use App\Services\GameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GamePlay extends Component
{
    public Game $game;
    public ?Scene $scene = null;
    public array $availableChoices = [];
    public array $currentState = [];
    public array $gameHistory = [];
    public bool $isFinished = false;
    public array $lastEvents = [];
    public array $triggeredEvents = [];
    public int $renderKey = 0;

    protected GameService $gameService;

    public function boot(GameService $gameService): void
    {
        $this->gameService = $gameService;
    }

    /**
     * Инициализация игры
     */
    public function mount(int $scenarioId, string $difficulty = 'easy'): void
    {
        // Завершаем все активные игры пользователя
        Game::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->update(['status' => 'completed']);

        // Создаём новую игру
        $this->game = $this->gameService->startGame(
            Auth::id(),
            $scenarioId,
            $difficulty
        );

        $this->renderKey = 0;
        $this->loadGameData();
    }

    /**
     * Загрузить данные игры
     */
    public function loadGameData(): void
    {
        Log::info('loadGameData начат', [
            'game_id' => $this->game->id,
            'current_scene_id' => $this->game->current_scene_id,
            'status' => $this->game->status,
        ]);

        // Проверяем статус игры
        if ($this->game->isFinished()) {
            $this->isFinished = true;
            $this->renderKey++;
            return;
        }

        // Загружаем текущую сцену
        $this->scene = $this->gameService->getCurrentScene($this->game);

        Log::info('loadGameData: сцена загружена', [
            'scene_id' => $this->scene->id ?? null,
            'scene_title' => $this->scene->title ?? null,
            'scene_order' => $this->scene->order ?? null,
        ]);

        if (!$this->scene) {
            $this->isFinished = true;
            $this->game->update(['status' => 'completed']);
            $this->renderKey++;
            return;
        }

        // Загружаем состояние
        $this->currentState = $this->game->getCurrentState(true);

        // Загружаем доступные выборы
        $this->availableChoices = $this->gameService->getAvailableChoices($this->game);

        // Загружаем историю (последние 10 событий)
        $this->loadHistory();

        // Очищаем предыдущие события
        $this->triggeredEvents = [];

        $this->renderKey++;
    }

    /**
     * Загрузить историю игры
     */
    private function loadHistory(): void
    {
        $this->gameHistory = $this->game->gameHistories()
            ->with('event')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($history) {
                return [
                    'event_name' => $history->event->name ?? 'Unknown Event',
                    'event_description' => $history->event->description ?? '',
                    'created_at' => $history->created_at->format('H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Обработка выбора
     */
    public function selectChoice(int $choiceId): void
    {
        try {
            Log::info('selectChoice начат', [
                'game_id' => $this->game->id,
                'choice_id' => $choiceId,
                'current_scene_id' => $this->game->current_scene_id,
            ]);

            // Обрабатываем выбор
            $result = $this->gameService->makeChoice($this->game->id, $choiceId);

            Log::info('selectChoice: результат получен', [
                'new_scene_id' => $result['next_scene']->id ?? null,
                'new_scene_title' => $result['next_scene']->title ?? null,
            ]);

            // Полностью перезагружаем игру из БД
            $this->game = Game::with([
                'currentScene.choices.event.effects.effectType',
                'gameHistories.event.effects.effectType'
            ])->find($this->game->id);

            // Принудительно очищаем кэш состояния
            Cache::forget("game_state_{$this->game->id}");
            Cache::forget("game_{$this->game->id}");

            // Сохраняем события от акторов для отображения
            $this->triggeredEvents = array_map(function ($trigger) {
                return [
                    'actor_name' => $trigger['actor_name'] ?? 'Актор',
                    'event_name' => $trigger['event_name'] ?? 'Событие',
                    'trigger_key' => $trigger['trigger_key'] ?? '',
                    'trigger_value' => $trigger['trigger_value'] ?? '',
                    'messages' => $trigger['messages'] ?? [],
                ];
            }, $result['triggered_events'] ?? []);

            // Загружаем данные заново
            $this->scene = $this->game->currentScene;
            $this->currentState = $result['new_state'];
            $this->availableChoices = $this->gameService->getAvailableChoices($this->game);
            $this->loadHistory();

            Log::info('selectChoice: данные обновлены', [
                'new_scene_id' => $this->scene->id ?? null,
                'new_scene_title' => $this->scene->title ?? null,
                'available_choices_count' => count($this->availableChoices),
            ]);

            // Проверяем, не завершена ли игра
            if ($this->game->isFinished()) {
                $this->isFinished = true;
                session()->flash('message', 'Игра завершена!');
            } else {
                session()->flash('message', 'Выбор сделан успешно!');
            }

            // Принудительно обновляем компонент
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            Log::error('selectChoice ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Получить процентное значение параметра для отображения
     */
    public function getParameterValue(string $key): int
    {
        return $this->currentState[$key] ?? 0;
    }

    /**
     * Получить цвет для параметра
     */
    public function getParameterColor(int $value): string
    {
        if ($value >= 80) return 'green';
        if ($value >= 60) return 'blue';
        if ($value >= 40) return 'yellow';
        if ($value >= 20) return 'orange';
        return 'red';
    }

    /**
     * Получить название параметра на русском
     */
    public function getParameterLabel(string $key): string
    {
        $labels = [
            'Институциональная устойчивость' => 'Институциональная устойчивость',
            'Управляемость аппарата' => 'Управляемость аппарата',
            'Конфликтная напряженность' => 'Конфликтная напряженность',
            'Публичная легитимность' => 'Публичная легитимность',
            'Доверие к процедурам' => 'Доверие к процедурам',
            'Риск управленческого сбоя' => 'Риск управленческого сбоя',
            'Горизонт устойчивости' => 'Горизонт устойчивости',
        ];

        return $labels[$key] ?? $key;
    }

    /**
     * Получить дополнительные данные сцены
     */
    public function getAdditionalData(): array
    {
        if (!$this->scene) {
            return [];
        }

        $data = json_decode($this->scene->additional_data, true);

        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Принудительно начать новую игру
     */
    public function forceNewGame(): void
    {
        $scenarioId = $this->game->currentScene->scenario_id;
        $difficulty = $this->game->difficulty;

        $this->game->update(['status' => 'completed']);
        Cache::forget("game_state_{$this->game->id}");
        Cache::forget("game_{$this->game->id}");

        $this->game = $this->gameService->startGame(
            Auth::id(),
            $scenarioId,
            $difficulty
        );

        $this->loadGameData();
        session()->flash('message', 'Начата новая игра!');
    }

    public function render()
    {
        return view('livewire.game-play')
            ->layout('layouts.app')
            ->title('Игра');
    }
}
