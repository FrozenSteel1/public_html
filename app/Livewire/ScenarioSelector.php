<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Scenario;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ScenarioSelector extends Component
{
    public array $scenarios = [];
    public array $selectedDifficulties = [];
    public string $search = '';
    public ?int $pendingScenarioId = null;
    public ?array $activeGame = null;
    public bool $showModal = false;

    public function mount(): void
    {
        $this->loadScenarios();
        $this->checkActiveGame();
    }

    /**
     * Загрузить сценарии
     */
    public function loadScenarios(): void
    {
        $query = Scenario::with('presets')
            ->orderBy('name');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->scenarios = $query->get()->map(function ($scenario) {
            return [
                'id' => $scenario->id,
                'name' => $scenario->name,
                'description' => $scenario->description,
                'difficulty' => $scenario->difficulty,
                'presets' => $scenario->presets->map(function ($preset) {
                    return [
                        'difficulty' => $preset->difficulty,
                        'settings' => $preset->settings,
                    ];
                })->toArray(),
                'scenes_count' => $scenario->scenes()->count(),
            ];
        })->toArray();
    }

    /**
     * Проверить активную игру пользователя
     */
    public function checkActiveGame(): void
    {
        $game = Game::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->with('currentScene')
            ->first();

        if ($game) {
            $scenario = Scenario::whereHas('scenes', function ($query) use ($game) {
                $query->where('id', $game->current_scene_id);
            })->first();

            $this->activeGame = [
                'id' => $game->id,
                'scenario_name' => $scenario->name ?? 'Неизвестный сценарий',
                'current_scene_title' => $game->currentScene->title ?? 'Продолжить',
                'created_at' => $game->created_at->format('d.m.Y H:i'),
                'difficulty' => $game->difficulty,
                'steps' => $game->gameHistories()->count(),
            ];
        } else {
            $this->activeGame = null;
        }
    }

    /**
     * Попытка начать игру
     */
    public function tryStartGame(int $scenarioId): void
    {
        // Если есть активная игра - показываем модальное окно
        if ($this->activeGame) {
            $this->pendingScenarioId = $scenarioId;
            $this->showModal = true;
        } else {
            // Если нет активной игры - сразу стартуем
            $this->startNewGame($scenarioId);
        }
    }

    /**
     * Начать новую игру (завершить старую)
     */
    public function startNewGame(int $scenarioId): void
    {
        $difficulty = $this->selectedDifficulties[$scenarioId] ?? 'easy';

        // Завершаем активную игру если есть
        if ($this->activeGame) {
            Game::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->update(['status' => 'completed']);
            $this->activeGame = null;
        }

        $this->showModal = false;
        $this->pendingScenarioId = null;

        redirect()->route('game.play', [
            'scenarioId' => $scenarioId,
            'difficulty' => $difficulty,
        ]);
    }

    /**
     * Продолжить активную игру
     */
    public function continueActiveGame(): void
    {
        if ($this->activeGame) {
            $this->showModal = false;
            $this->pendingScenarioId = null;
            redirect()->route('game.continue', [
                'gameId' => $this->activeGame['id'],
            ]);
        }
    }

    /**
     * Закрыть модальное окно
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->pendingScenarioId = null;
    }

    /**
     * Получить доступные сложности для сценария
     */
    public function getAvailableDifficulties(int $scenarioId): array
    {
        $scenario = collect($this->scenarios)->firstWhere('id', $scenarioId);

        if (!$scenario) {
            return [];
        }

        return array_map(function ($preset) {
            return $preset['difficulty'];
        }, $scenario['presets']);
    }

    /**
     * Получить цвет для сложности
     */
    public function getDifficultyColor(string $difficulty): string
    {
        return match($difficulty) {
            'easy' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'hard' => 'bg-orange-100 text-orange-800',
            'expert' => 'bg-red-100 text-red-800',
            'custom' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Получить название сложности на русском
     */
    public function getDifficultyLabel(string $difficulty): string
    {
        return match($difficulty) {
            'easy' => 'Лёгкий',
            'medium' => 'Средний',
            'hard' => 'Сложный',
            'expert' => 'Экспертный',
            'custom' => 'Пользовательский',
            default => $difficulty,
        };
    }

    public function render()
    {
        return view('livewire.scenario-selector')
            ->layout('layouts.app')
            ->title('Выбор сценария');
    }
}
