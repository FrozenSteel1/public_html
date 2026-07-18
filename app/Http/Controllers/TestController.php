<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Показать информацию об игре
     */
    public function showGame(int $gameId)
    {
        $game = $this->gameService->getGame($gameId);
        $state = $game->getCurrentState();
        $scene = $game->currentScene;
        $choices = $this->gameService->getAvailableChoices($game);
        $history = $game->gameHistories()->with('event')->orderBy('id')->get();

        return response()->json([
            'game' => [
                'id' => $game->id,
                'status' => $game->status,
                'difficulty' => $game->difficulty,
                'created_at' => $game->created_at,
            ],
            'current_scene' => $scene ? [
                'id' => $scene->id,
                'title' => $scene->title,
                'order' => $scene->order,
            ] : null,
            'state' => $state,
            'available_choices' => $choices->map(function ($choice) {
                return [
                    'id' => $choice->id,
                    'description' => $choice->description,
                    'event_id' => $choice->event_id,
                ];
            }),
            'history' => $history->map(function ($history) {
                return [
                    'event_name' => $history->event->name,
                    'event_description' => $history->event->description,
                    'created_at' => $history->created_at,
                ];
            }),
        ]);
    }

    /**
     * Показать только состояние игры
     */
    public function showState(int $gameId)
    {
        $game = $this->gameService->getGame($gameId);
        $state = $game->getCurrentState();

        return response()->json([
            'game_id' => $gameId,
            'status' => $game->status,
            'state' => $state,
        ]);
    }
}
