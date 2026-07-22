<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Scenario;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserGames extends Component
{
    public array $games = [];
    public array $scenarios = [];
    public bool $showAll = false;

    public function mount(): void
    {
        $this->loadGames();
    }

    public function loadGames(): void
    {
        $query = Game::where('user_id', Auth::id());

        if (!$this->showAll) {
            $query->where('status', 'in_progress');
        }

        $this->games = $query
            ->with(['currentScene', 'gameHistories' => function ($q) {
                $q->orderBy('id', 'desc')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($game) {
                $scenario = Scenario::whereHas('scenes', function ($q) use ($game) {
                    $q->where('id', $game->current_scene_id);
                })->first();

                return [
                    'id' => $game->id,
                    'status' => $game->status,
                    'difficulty' => $game->difficulty,
                    'current_scene_title' => $game->currentScene->title ?? 'Завершена',
                    'scenario_name' => $scenario->name ?? 'Неизвестный сценарий',
                    'created_at' => $game->created_at->format('d.m.Y H:i'),
                    'steps_count' => $game->gameHistories()->count(),
                    'is_finished' => $game->isFinished(),
                ];
            })
            ->toArray();
    }

    public function continueGame(int $gameId): void
    {
        $game = Game::where('user_id', Auth::id())->find($gameId);

        if (!$game) {
            session()->flash('error', 'Игра не найдена');
            return;
        }

        if ($game->isFinished()) {
            session()->flash('error', 'Эта игра уже завершена');
            return;
        }

        redirect()->route('game.continue', ['gameId' => $gameId]);
    }

    public function deleteGame(int $gameId): void
    {
        $game = Game::where('user_id', Auth::id())->find($gameId);

        if ($game) {
            $game->delete();
            session()->flash('message', 'Игра удалена');
            $this->loadGames();
        }
    }

    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;
        $this->loadGames();
    }

    public function render()
    {
        return view('livewire.user-games')
            ->layout('layouts.app')
            ->title('Мои игры');
    }
}
