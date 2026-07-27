<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Scene;
use App\Models\Scenario;
use App\Services\GameService;
use App\Services\Effects\EffectManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\GameHistory;

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
    public array $gameMessages = [];
    public bool $messageShown = false;
    public array $pendingResult = [];
    public bool $showMessageModal = false;
    public array $currentModalMessage = [];
    public array $sceneActors = [];
    public array $gameHistoryWithMonths = [];
    public int $timerKey = 0;
    public int $timeLimit = 60;
    public array $monthNames = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];
    protected GameService $gameService;
    protected EffectManager $effectManager;
    public array $delayedMessages = [];
    public bool $hasDelayedMessages = false;
    public bool $showDelayedModal = false;
    public function boot(GameService $gameService): void
    {
        $this->gameService = $gameService;
        $this->effectManager = new EffectManager();
    }

    public function mount(...$parameters): void
    {
        $params = request()->route()->parameters();

        if (isset($params['gameId'])) {
            $this->continueGame((int) $params['gameId']);
            return;
        }

        if (isset($params['scenarioId'])) {
            $this->startNewGame((int) $params['scenarioId'], $params['difficulty'] ?? 'easy');
            return;
        }

        throw new \Exception("Не указан сценарий или ID игры");
    }

    #[On('time-expired')]
    public function timeExpired(): void
    {
        Log::info('Время вышло!', [
            'game_id' => $this->game->id,
            'scene_id' => $this->scene->id ?? null,
        ]);

        $defaultChoice = $this->scene->choices()
            ->where('description', 'LIKE', 'Ничего не делать')
            ->first();

        if ($defaultChoice) {
            $this->selectChoice($defaultChoice->id);
            return;
        }

        $lastChoice = $this->scene->choices()
            ->orderBy('order', 'desc')
            ->first();

        if ($lastChoice) {
            $this->selectChoice($lastChoice->id);
        }
    }

    public function restartTimer(): void
    {
        if ($this->scene) {
            $this->timeLimit = $this->gameService->getTimeLimit(
                $this->scene->id,
                $this->game->difficulty,
                Auth::id()
            );
            $this->dispatch('timer-restart', seconds: $this->timeLimit);
            Log::info('Таймер обновлён', [
                'seconds' => $this->timeLimit,
                'scene' => $this->scene->title,
            ]);
        }
    }

    private function startNewGame(int $scenarioId, string $difficulty): void
    {
        Game::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->update(['status' => 'completed']);

        $this->game = $this->gameService->startGame(
            Auth::id(),
            $scenarioId,
            $difficulty
        );

        $this->renderKey = 0;
        $this->loadGameData();
    }

    private function continueGame(int $gameId): void
    {
        $this->game = Game::with([
            'currentScene.choices.event.effects.effectType',
            'gameHistories.event.effects.effectType'
        ])->find($gameId);

        if (!$this->game) {
            throw new \Exception("Игра с ID {$gameId} не найдена");
        }

        if ($this->game->user_id !== Auth::id()) {
            throw new \Exception("У вас нет доступа к этой игре");
        }

        if ($this->game->isFinished()) {
            session()->flash('error', 'Эта игра уже завершена');
            redirect()->route('user.games');
            return;
        }

        $this->loadGameData();
    }

    public function loadGameData(): void
    {
        Log::info('loadGameData начат', [
            'game_id' => $this->game->id,
            'current_scene_id' => $this->game->current_scene_id,
            'status' => $this->game->status,
        ]);

        if ($this->game->isFinished()) {
            $this->isFinished = true;
            $this->renderKey++;
            return;
        }

        $this->scene = $this->gameService->getCurrentScene($this->game);

        if (!$this->scene) {
            $this->isFinished = true;
            $this->game->update(['status' => 'completed']);
            $this->renderKey++;
            return;
        }

        $this->currentState = $this->game->getCurrentState(true);
        $this->availableChoices = $this->gameService->getAvailableChoices($this->game);

        $this->loadHistoryWithMonths();
        $this->loadSceneActors();
        $this->triggeredEvents = [];

        // ========== ПРОВЕРКА ОТЛОЖЕННЫХ СООБЩЕНИЙ ==========
        $this->checkDelayedMessages();

        $this->restartTimer();

        $this->dispatch('console-log', [
            'type' => 'info',
            'title' => '🔄 ЗАГРУЗКА ИГРЫ',
            'game_id' => $this->game->id,
            'current_scene' => $this->scene->title ?? 'unknown',
            'state' => $this->currentState,
            'available_choices_count' => count($this->availableChoices),
            'has_delayed_messages' => $this->hasDelayedMessages,
            'delayed_messages_count' => count($this->delayedMessages),
        ]);

        $this->renderKey++;
    }
    public function openDelayedModal(): void
    {
        $this->showDelayedModal = true;
    }

    public function closeDelayedModal(): void
    {
        $this->showDelayedModal = false;
        $this->hasDelayedMessages = false;
        $this->delayedMessages = [];
    }
    private function checkDelayedMessages(): void
    {
        Log::info('checkDelayedMessages: начат');

        $readyMessages = $this->gameService->getReadyDelayedMessages();

        Log::info('checkDelayedMessages: результат', [
            'readyMessages' => $readyMessages,
        ]);

        if (!empty($readyMessages)) {
            $this->delayedMessages = $readyMessages;
            $this->hasDelayedMessages = true;
            Log::info('Найдены созревшие отложенные сообщения', [
                'count' => count($readyMessages),
                'messages' => $readyMessages,
            ]);
        } else {
            $this->hasDelayedMessages = false;
            $this->delayedMessages = [];
            Log::info('Нет созревших отложенных сообщений');
        }
    }
    private function loadHistory(): void
    {
        $this->gameHistory = $this->game->gameHistories()
            ->with('event')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($history) {
                $sourceLabel = match($history->source) {
                    GameHistory::SOURCE_PLAYER => '👤 Игрок',
                    GameHistory::SOURCE_ACTOR => '🎭 Актор',
                    GameHistory::SOURCE_SYSTEM => '⚙️ Система',
                    default => '',
                };

                return [
                    'event_name' => $history->event->name ?? 'Unknown Event',
                    'event_description' => $history->event->description ?? '',
                    'created_at' => $history->created_at->format('H:i:s'),
                    'source' => $history->source,
                    'source_label' => $sourceLabel,
                ];
            })
            ->toArray();
    }

    private function loadHistoryWithMonths(): void
    {
        $histories = $this->game->gameHistories()
            ->with('event')
            ->orderBy('id', 'asc')
            ->get();

        if ($histories->isEmpty()) {
            $this->gameHistoryWithMonths = [];
            $this->gameHistory = [];
            return;
        }

        $result = [];
        $playerMoveCount = 0;
        $currentMonth = '';
        $processedEvents = [];

        foreach ($histories as $history) {
            $eventName = $history->event->name ?? '';

            // ========== ОПРЕДЕЛЯЕМ ПО source, А НЕ ПО НАЗВАНИЮ! ==========
            $isPlayerMove = $history->source === GameHistory::SOURCE_PLAYER;
            $isActorEvent = $history->source === GameHistory::SOURCE_ACTOR;
            $isSystemEvent = $history->source === GameHistory::SOURCE_SYSTEM;

            if ($isPlayerMove) {
                // Игровой ход
                $playerMoveCount++;
                $monthIndex = (($playerMoveCount - 1) % 12) + 1;
                $currentMonth = $this->monthNames[$monthIndex] ?? 'Месяц ' . $monthIndex;
                $processedEvents = [];

                $result[] = [
                    'event_name' => $eventName,
                    'event_description' => $history->event->description ?? '',
                    'month' => $currentMonth,
                    'step' => $playerMoveCount,
                    'is_actor_event' => false,
                    'created_at' => $history->created_at->format('H:i:s'),
                    'actor_type' => null,
                    'actor_name' => null,
                    'source' => $history->source,
                ];

            } elseif ($isActorEvent) {
                // Реакция актора
                $actionType = $this->extractActorAction($eventName);
                $actorName = $this->extractActorName($eventName);
                $eventKey = $eventName;

                // Избегаем дублирования одинаковых реакций в одном шаге
                if (!isset($processedEvents[$eventKey])) {
                    $processedEvents[$eventKey] = true;

                    $result[] = [
                        'event_name' => $eventName,
                        'event_description' => $history->event->description ?? '',
                        'month' => $currentMonth ?: 'Январь',
                        'step' => $playerMoveCount,
                        'is_actor_event' => true,
                        'created_at' => $history->created_at->format('H:i:s'),
                        'actor_type' => $actionType,
                        'actor_name' => $actorName,
                        'source' => $history->source,
                    ];
                }
            }
            // Системные события (source === system) пропускаем
        }

        $this->gameHistoryWithMonths = $result;
        $this->gameHistory = array_slice(array_reverse($this->gameHistoryWithMonths), 0, 10);
    }

    private function loadSceneActors(): void
    {
        $this->sceneActors = [];

        $actors = \App\Models\Actor::all();

        $additionalData = $this->scene->additional_data;
        if (is_string($additionalData)) {
            $additionalData = json_decode($additionalData, true);
        }

        $actorIds = $additionalData['actors'] ?? [];

        if (!empty($actorIds)) {
            $filteredActors = $actors->filter(function ($actor) use ($actorIds) {
                return in_array($actor->id, $actorIds);
            });
        } else {
            $filteredActors = $actors;
        }

        $this->sceneActors = $filteredActors->map(function ($actor) {
            $settings = $actor->settings;

            if (is_string($settings)) {
                $settings = json_decode($settings, true);
            }
            if (is_string($settings)) {
                $settings = json_decode($settings, true);
            }

            return [
                'id' => $actor->id,
                'name' => $actor->name,
                'description' => $actor->description,
                'settings' => is_array($settings) ? $settings : [],
                'triggers' => $actor->triggers,
            ];
        })->toArray();
    }

    private function isActorEvent(string $eventName): bool
    {
        $actorKeywords = ['актор', 'Актор', 'Блокирует', 'Поддерживает', 'Содействует',
            'Критикует', 'Тормозит', 'Отходит', 'Подает сигнал'];

        foreach ($actorKeywords as $keyword) {
            if (strpos($eventName, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private function extractActorAction(string $eventName): string
    {
        $actions = ['Блокирует', 'Поддерживает', 'Содействует', 'Критикует', 'Тормозит', 'Отходит', 'Подает сигнал'];
        foreach ($actions as $action) {
            if (strpos($eventName, $action) !== false) {
                return $action;
            }
        }
        return 'Действие';
    }

    private function extractActorName(string $eventName): string
    {
        $parts = explode(' - ', $eventName);
        if (count($parts) > 0) {
            $name = str_replace('актор', '', $parts[0]);
            $name = str_replace('Актор', '', $name);
            return trim($name);
        }
        return 'Актор';
    }

    private function getAppliedEffects(array $result): array
    {
        $effects = [];

        if (isset($result['choice']) && isset($result['choice']->event)) {
            foreach ($result['choice']->event->effects as $effect) {
                $effects[] = [
                    'type' => $effect->effectType->name ?? 'unknown',
                    'data' => $effect->effect_data,
                ];
            }
        }

        return $effects;
    }
    private function finishGame(): void
    {
        $this->isFinished = true;
        $this->renderKey++;
        session()->flash('message', 'Игра завершена!');

        // Редирект на страницу результатов
        redirect()->route('game.results', ['gameId' => $this->game->id]);
    }
    public function selectChoice(int $choiceId): void
    {
        try {
            Log::info('selectChoice начат', [
                'game_id' => $this->game->id,
                'choice_id' => $choiceId,
                'current_scene_id' => $this->game->current_scene_id,
            ]);

            // ========== ПРОВЕРКА СЕССИИ ДО ==========
            Log::info('Сессия ДО makeChoice', [
                'game_messages' => session()->get('game_messages', []),
                'all_session' => session()->all(),
            ]);

            $this->dispatch('console-log', [
                'type' => 'info',
                'title' => '🔍 СОСТОЯНИЕ ДО ВЫБОРА',
                'state' => $this->currentState,
                'choice_id' => $choiceId,
            ]);

            $result = $this->gameService->makeChoice($this->game->id, $choiceId);

            Log::info('selectChoice: результат получен', [
                'new_scene_id' => $result['next_scene']->id ?? null,
                'new_scene_title' => $result['next_scene']->title ?? null,
            ]);

            // ========== ПРОВЕРКА СЕССИИ ПОСЛЕ ==========
            Log::info('Сессия ПОСЛЕ makeChoice', [
                'game_messages' => session()->get('game_messages', []),
            ]);

            $this->game = $result['game'];

            $this->loadHistoryWithMonths();
            $this->loadSceneActors();

            $this->dispatch('console-log', [
                'type' => 'success',
                'title' => '✅ СОСТОЯНИЕ ПОСЛЕ ВЫБОРА',
                'new_state' => $result['new_state'],
                'applied_effects' => $this->getAppliedEffects($result),
                'triggered_events' => $result['triggered_events'] ?? [],
            ]);

            $this->triggeredEvents = array_map(function ($trigger) {
                return [
                    'actor_name' => $trigger['actor_name'] ?? 'Актор',
                    'event_name' => $trigger['event_name'] ?? 'Событие',
                    'trigger_key' => $trigger['trigger_key'] ?? '',
                    'trigger_value' => $trigger['trigger_value'] ?? '',
                    'messages' => $trigger['messages'] ?? [],
                ];
            }, $result['triggered_events'] ?? []);

            // ========== ПОЛУЧАЕМ СООБЩЕНИЯ ==========
            $this->gameMessages = $this->effectManager->getMessages();

            // Если сообщений нет — проверяем сессию напрямую
            if (empty($this->gameMessages)) {
                $this->gameMessages = session()->get('game_messages', []);
                Log::info('Сообщения из сессии напрямую', ['messages' => $this->gameMessages]);
            }

            Log::info('Сообщения в selectChoice', [
                'game_messages' => $this->gameMessages,
            ]);

            // Если есть сообщения — показываем модальное окно
            if (count($this->gameMessages) > 0) {
                $this->currentModalMessage = $this->gameMessages[0];
                $this->showMessageModal = true;
                $this->messageShown = false;

                $this->pendingResult = [
                    'game' => $this->game,
                    'next_scene' => $result['next_scene'],
                    'new_state' => $result['new_state'],
                    'triggered_events' => $result['triggered_events'],
                ];

                Log::info('Модальное окно открыто', [
                    'message' => $this->currentModalMessage['text'],
                ]);

                $this->renderKey++;
                return;
            }

            // Если сообщений нет — сразу применяем результат
            $this->applyGameResult($result);

        } catch (\Exception $e) {
            Log::error('selectChoice ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    private function applyGameResult(array $result): void
    {
        Log::info('applyGameResult начат', [
            'game_id' => $this->game->id,
            'next_scene_id' => $result['next_scene']->id ?? null,
        ]);

        $this->game = Game::with([
            'currentScene.choices.event.effects.effectType',
            'gameHistories.event.effects.effectType'
        ])->find($this->game->id);

        Cache::forget("game_state_{$this->game->id}");
        Cache::forget("game_{$this->game->id}");

        $this->scene = $this->game->currentScene;
        $this->currentState = $result['new_state'] ?? $this->game->getCurrentState(true);
        $this->availableChoices = $this->gameService->getAvailableChoices($this->game);

        // Если сцены нет или игра завершена
        if ($this->game->isFinished() || !$this->scene) {
            $this->finishGame();
            return;
        }

        $this->loadHistoryWithMonths();
        $this->loadSceneActors();

        // ========== ПРОВЕРКА ОТЛОЖЕННЫХ СООБЩЕНИЙ ==========
        $this->checkDelayedMessages();

        Log::info('applyGameResult: данные обновлены', [
            'new_scene_id' => $this->scene->id ?? null,
            'new_scene_title' => $this->scene->title ?? null,
            'available_choices_count' => count($this->availableChoices),
            'history_count' => count($this->gameHistoryWithMonths),
            'has_delayed_messages' => $this->hasDelayedMessages,
        ]);

        $this->renderKey++;
        $this->restartTimer();
    }

    public function closeMessageModal(): void
    {
        Log::info('closeMessageModal вызван', [
            'has_pending_result' => !empty($this->pendingResult),
        ]);

        $this->showMessageModal = false;
        $this->messageShown = true;

        if (!empty($this->pendingResult)) {
            $this->applyGameResult($this->pendingResult);
            $this->pendingResult = [];
        } else {
            $this->loadGameData();
        }

        $this->dispatch('$refresh');
    }



    public function getParameterValue(string $key): int
    {
        return $this->currentState[$key] ?? 0;
    }

    public function getParameterColor(int $value): string
    {
        if ($value >= 80) return 'green';
        if ($value >= 60) return 'blue';
        if ($value >= 40) return 'yellow';
        if ($value >= 20) return 'orange';
        return 'red';
    }

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

    public function checkModal(): void
    {
        // Просто проверяем состояние, ничего не делаем
    }

    public function render()
    {
        return view('livewire.game-play')
            ->layout('layouts.app')
            ->title('Игра');
    }
}
