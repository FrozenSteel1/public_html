<?php

namespace App\Livewire;

use App\Models\Scenario;
use Livewire\Component;

class ScenarioSelector extends Component
{
    public array $scenarios = [];
    public array $selectedDifficulties = [];
    public string $search = '';

    public function mount(): void
    {
        $this->loadScenarios();
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
     * Начать игру
     */
    public function startGame(int $scenarioId): void
    {
        $difficulty = $this->selectedDifficulties[$scenarioId] ?? 'easy';

        redirect()->route('game.play', [
            'scenarioId' => $scenarioId,
            'difficulty' => $difficulty,
        ]);
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
