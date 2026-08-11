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
    public int $selectedScenarioId = 0;
    public ?string $toast = null;

    /** Сложности по макету UI V1 (экран 01): подпись + «уровень» шкалы */
    public const DIFFICULTIES = [
        'easy'   => ['label' => 'Слабая',      'level' => 2],
        'medium' => ['label' => 'Средняя',     'level' => 3],
        'hard'   => ['label' => 'Сильная',     'level' => 4],
        'expert' => ['label' => 'Критическая', 'level' => 5],
    ];

    public function mount(): void
    {
        $this->loadScenarios();
        $this->checkActiveGame();
    }

    public function updatedSearch(): void
    {
        $this->loadScenarios();
    }

    /**
     * Загрузить сценарии
     */
    public function loadScenarios(): void
    {
        $query = Scenario::with('presets')->orderBy('name');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->scenarios = $query->get()->map(function ($scenario) {
            return [
                'id'           => $scenario->id,
                'name'         => $scenario->name,
                'description'  => $scenario->description,
                'difficulty'   => $scenario->difficulty,
                'presets'      => $scenario->presets->map(fn ($preset) => [
                    'difficulty' => $preset->difficulty,
                    'settings'   => $preset->settings,
                ])->toArray(),
                'scenes_count' => $scenario->scenes()->count(),
            ];
        })->toArray();

        // Выбранный сценарий — первый доступный (имеющий пресеты)
        if (!collect($this->scenarios)->firstWhere('id', $this->selectedScenarioId)) {
            $first = collect($this->scenarios)->first(fn ($s) => count($s['presets']) > 0)
                ?? collect($this->scenarios)->first();
            $this->selectedScenarioId = $first['id'] ?? 0;
        }

        // Сложность по умолчанию — первый доступный пресет сценария
        foreach ($this->scenarios as $scenario) {
            if (!isset($this->selectedDifficulties[$scenario['id']])) {
                $this->selectedDifficulties[$scenario['id']] = $scenario['presets'][0]['difficulty'] ?? 'easy';
            }
        }
    }

    /**
     * Выбор сценария карточкой в сетке (только доступные)
     */
    public function selectScenario(int $scenarioId): void
    {
        $scenario = collect($this->scenarios)->firstWhere('id', $scenarioId);

        if (!$scenario || count($scenario['presets']) === 0) {
            return; // «СКОРО» — недоступно
        }

        $this->selectedScenarioId = $scenarioId;
    }

    /**
     * Выбор сложности (только из доступных пресетов сценария)
     */
    public function selectDifficulty(string $difficulty): void
    {
        if (!$this->selectedScenarioId || !array_key_exists($difficulty, self::DIFFICULTIES)) {
            return;
        }

        if (!in_array($difficulty, $this->getAvailableDifficulties($this->selectedScenarioId), true)) {
            return;
        }

        $this->selectedDifficulties[$this->selectedScenarioId] = $difficulty;
    }

    /**
     * Предпросмотр (поведение макета — тост)
     */
    public function preview(): void
    {
        $this->toast = 'Предпросмотр сценария будет добавлен позже.';
    }

    public function clearToast(): void
    {
        $this->toast = null;
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
                'id'                  => $game->id,
                'scenario_name'       => $scenario->name ?? 'Неизвестный сценарий',
                'current_scene_title' => $game->currentScene->title ?? 'Продолжить',
                'created_at'          => $game->created_at->format('d.m.Y H:i'),
                'difficulty'          => $game->difficulty,
                'steps'               => $game->gameHistories()->count(),
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
        if (!$scenarioId) {
            return;
        }

        if ($this->activeGame) {
            $this->pendingScenarioId = $scenarioId;
            $this->showModal = true;
        } else {
            $this->startNewGame($scenarioId);
        }
    }

    /**
     * Начать новую игру (завершить старую)
     */
    public function startNewGame(int $scenarioId): void
    {
        $difficulty = $this->selectedDifficulties[$scenarioId] ?? 'easy';

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

            redirect()->route('game.continue', ['gameId' => $this->activeGame['id']]);
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
     * Доступные сложности для сценария (из пресетов)
     */
    public function getAvailableDifficulties(int $scenarioId): array
    {
        $scenario = collect($this->scenarios)->firstWhere('id', $scenarioId);

        if (!$scenario) {
            return [];
        }

        return array_map(fn ($preset) => $preset['difficulty'], $scenario['presets']);
    }

    /**
     * Название сложности по макету (решение A)
     */
    public function getDifficultyLabel(string $difficulty): string
    {
        return self::DIFFICULTIES[$difficulty]['label']
            ?? ($difficulty === 'custom' ? 'Пользовательский' : $difficulty);
    }

    public function getDifficultyLevel(string $difficulty): int
    {
        return self::DIFFICULTIES[$difficulty]['level'] ?? 3;
    }

    /**
     * Цвет бейджа сложности (оставлен для обратной совместимости со старой вью)
     */
    public function getDifficultyColor(string $difficulty): string
    {
        return match ($difficulty) {
            'easy'   => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'hard'   => 'bg-orange-100 text-orange-800',
            'expert' => 'bg-red-100 text-red-800',
            'custom' => 'bg-purple-100 text-purple-800',
            default  => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        $selected = collect($this->scenarios)->firstWhere('id', $this->selectedScenarioId);
        $others   = collect($this->scenarios)
            ->filter(fn ($s) => $s['id'] !== $this->selectedScenarioId)
            ->values();

        return view('livewire.scenario-selector', [
            'selected' => $selected,
            'others'   => $others,
        ])
            ->layout('layouts.game')
            ->title('Выбор сценария');
    }
}
