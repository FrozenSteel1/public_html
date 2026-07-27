<?php

namespace App\Livewire;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlayerDashboard extends Component
{
    public array $games = [];
    public array $statistics = [];
    public bool $hasActiveGame = false;
    public ?Game $activeGame = null;

    public function mount(): void
    {
        $this->loadGames();
        $this->loadStatistics();
    }

    private function loadGames(): void
    {
        $userId = Auth::id();

        // Загружаем все игры пользователя, сортируем по дате
        $games = Game::where('user_id', $userId)
            ->with('currentScene')
            ->orderBy('created_at', 'desc')
            ->get();

        // Проверяем, есть ли активная игра
        $activeGame = $games->firstWhere('status', 'in_progress');

        if ($activeGame) {
            $this->hasActiveGame = true;
            $this->activeGame = $activeGame;
        }

        $this->games = $games->map(function ($game) {
            return [
                'id' => $game->id,
                'scenario_name' => $this->getScenarioName($game),
                'difficulty' => $this->getDifficultyLabel($game->difficulty),
                'status' => $game->status,
                'status_label' => $this->getStatusLabel($game->status),
                'status_color' => $this->getStatusColor($game->status),
                'created_at' => $game->created_at->format('d.m.Y H:i'),
                'current_scene_title' => $game->currentScene->title ?? 'Неизвестно',
                'is_active' => $game->status === 'in_progress',
            ];
        })->toArray();
    }

    private function loadStatistics(): void
    {
        $userId = Auth::id();

        $totalGames = Game::where('user_id', $userId)->count();
        $completedGames = Game::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
        $failedGames = Game::where('user_id', $userId)
            ->where('status', 'failed')
            ->count();

        // Средний прогресс по завершенным играм (примерная оценка)
        $avgProgress = 0;
        if ($completedGames > 0) {
            // Можно подсчитать средний процент пройденных сцен
            // Для простоты показываем количество завершенных игр
            $avgProgress = $completedGames;
        }

        $this->statistics = [
            'total_games' => $totalGames,
            'completed_games' => $completedGames,
            'failed_games' => $failedGames,
            'in_progress_games' => $totalGames - $completedGames - $failedGames,
            'avg_progress' => $avgProgress,
        ];
    }

    private function getScenarioName(Game $game): string
    {
        // Получаем имя сценария через первую историю или текущую сцену
        if ($game->currentScene) {
            $scenario = $game->currentScene->scenario;
            if ($scenario) {
                return $scenario->name;
            }
        }

        // Если нет текущей сцены, ищем через историю
        $firstHistory = $game->gameHistories()->with('event')->first();
        if ($firstHistory) {
            $choice = \App\Models\Choice::where('event_id', $firstHistory->event_id)->first();
            if ($choice && $choice->scene) {
                $scenario = $choice->scene->scenario;
                if ($scenario) {
                    return $scenario->name;
                }
            }
        }

        return 'Неизвестный сценарий';
    }

    private function getDifficultyLabel(string $difficulty): string
    {
        return match($difficulty) {
            'easy' => 'Легкая',
            'medium' => 'Средняя',
            'hard' => 'Сложная',
            'expert' => 'Эксперт',
            'custom' => 'Пользовательская',
            default => $difficulty,
        };
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'started' => 'Начата',
            'in_progress' => 'В процессе',
            'completed' => 'Завершена',
            'failed' => 'Провалена',
            default => $status,
        };
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'started' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    public function continueGame(int $gameId): void
    {
        redirect()->route('game.play', ['gameId' => $gameId]);
    }

    public function viewResults(int $gameId): void
    {
        redirect()->route('game.results', ['gameId' => $gameId]);
    }

    public function startNewGame(): void
    {
        redirect()->route('scenarios');
    }

    public function render()
    {
        return view('livewire.player-dashboard')
            ->layout('layouts.app')
            ->title('Мои игры');
    }
}
