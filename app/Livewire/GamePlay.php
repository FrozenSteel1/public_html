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
use App\Support\PrototypeContent;

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
    /** Откуда открыта модалка сообщения: очередь хода или повторное открытие из «Входящих» */
    public bool $messageModalFromInbox = false;
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
    /** Сложности по макету UI V1 (экран 02): подпись + уровень + описание */
    public const DIFFICULTIES = [
        'easy'   => ['label' => 'Слабая',      'level' => 2, 'description' => 'Стартовый уровень для знакомства с механикой управления.'],
        'medium' => ['label' => 'Средняя',     'level' => 3, 'description' => 'Умеренный уровень управленческого давления.'],
        'hard'   => ['label' => 'Сильная',     'level' => 4, 'description' => 'Высокий уровень управленческого давления.'],
        'expert' => ['label' => 'Критическая', 'level' => 5, 'description' => 'Предельный уровень управленческого давления.'],
    ];
    protected GameService $gameService;
    protected EffectManager $effectManager;
    public array $delayedMessages = [];
    public bool $hasDelayedMessages = false;
    public bool $showDelayedModal = false;
    /** UI-фаза: closed-folder → gameplay (позже добавится introduction) */
    public string $phase = 'closed-folder';

    /** Данные играемого сценария (для экрана «Закрытая папка») */
    public array $scenarioInfo = [];
    /** Активная вкладка игрового досье (экраны 04+) */
    public string $gameplayTab = 'scenario';
    /** Выбранный вариант решения (экран 05) */
    public ?int $selectedChoiceId = null;

    /** Дедлайн таймера решений (unix timestamp) */
    public ?int $decisionDeadline = null;

    /** Время на решение истекло */
    public bool $decisionExpired = false;
    /** Каталог материалов (экран 06) */
    public string $materialCategory = 'all';
    public string $materialSort = 'importance';
    public string $materialView = 'cards';
    public ?string $activeMaterialId = null;
    public array $studiedMaterialIds = [];
    /** Каталог справок (экран 07) */
    public string $referenceCategory = 'all';
    public string $referenceSort = 'importance';
    public string $referenceView = 'cards';
    public ?string $activeReferenceId = null;
    public array $studiedReferenceIds = [];
    /** Каталог СМИ (экран 08) */
    public string $mediaCategory = 'all';
    public string $mediaSort = 'importance';
    public string $mediaView = 'cards';
    public ?string $activeMediaId = null;
    public array $reviewedMediaIds = [];
    /** Каталог обращений (экран 09) */
    public string $appealCategory = 'all';
    public string $appealSort = 'priority';
    public string $appealView = 'cards';
    public ?string $activeAppealId = null;
    public array $reviewedAppealIds = [];
    /** Документ в области «Решения»: decisions | result | inbox (экраны 11–12) */
    public string $decisionsDoc = 'decisions';

    /** Снимок результата последнего решения (экран 11) */
    public array $resultSnapshot = [];

    /** Входящие сообщения текущего хода (экран 12) */
    public array $inboxMessages = [];
    public array $readInboxIds = [];
    public string $inboxFilter = 'all';
    public string $inboxActorFilter = 'all';
    public string $inboxSort = 'unread-first';
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
        if ($this->decisionExpired) {
            return;
        }

        $this->decisionExpired = true;
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
        $this->resetDecisionState();

        // Если сцены нет или игра завершена
        $this->loadHistoryWithMonths();
        $this->loadSceneActors();
        $this->triggeredEvents = [];

        // ========== ПРОВЕРКА ОТЛОЖЕННЫХ СООБЩЕНИЙ ==========
        $this->checkDelayedMessages();

        $this->restartTimer();
        $this->loadScenarioInfo();
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
            Log::info('🚀 [GamePlay] selectChoice START', [
                'session_before_makeChoice' => session()->get('game_messages', []),
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
            // 🔎 ГЛАВНАЯ ПРОВЕРКА МАССИВА
            Log::info('🔎 [GamePlay] ФИНАЛЬНАЯ ПРОВЕРКА МАССИВА', [
                'game_messages_variable' => $this->gameMessages,
                'count' => count($this->gameMessages),
                'is_empty' => empty($this->gameMessages),
                'session_id' => session()->getId(),
            ]);
            $this->gameMessages = $this->effectManager->getMessages();
            if (empty($this->gameMessages)) {
                $this->gameMessages = session()->get('game_messages', []);
            }

            // ========== СНИМОК РЕЗУЛЬТАТА (ЭКРАН 11) ==========
            $this->resultSnapshot = [
                'decision' => collect($this->availableChoices)->firstWhere('id', $choiceId)?->description ?? 'Решение принято',
                'scene' => $result['next_scene']->title ?? '',
                'events' => collect($this->triggeredEvents)->map(fn ($t) => [
                    'actor' => $t['actor_name'] ?? 'Актор',
                    'event' => $t['event_name'] ?? 'Событие',
                ])->values()->toArray(),
                'messages' => $this->gameMessages,
            ];
            $this->inboxMessages = $this->normalizeMessages($this->gameMessages);
            $this->readInboxIds = [];

            // ========== ПРИМЕНЯЕМ РЕЗУЛЬТАТ СРАЗУ ==========
            // Сцена переключается в любом случае; модалка показывается поверх новой сцены.
            $this->applyGameResult($result);

            // Если есть сообщения — показываем модалку поверх новой сцены (очередь, по одному)
            if (count($this->gameMessages) > 0) {
                $this->currentModalMessage = $this->gameMessages[0];
                $this->showMessageModal = true;
                $this->messageShown = false;
                $this->messageModalFromInbox = false;
            }

        } catch (\Exception $e) {
            Log::error('selectChoice ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function closeMessageModal(): void
    {
        // Модалка открыта из «Входящих» (экран 12) — просто закрываем, остаёмся на списке
        if ($this->messageModalFromInbox) {
            $this->showMessageModal = false;
            $this->currentModalMessage = [];

            return;
        }

        // Очередь хода: если сообщения ещё есть — показываем следующее друг за другом
        if (count($this->gameMessages) > 1) {
            array_shift($this->gameMessages);
            $this->currentModalMessage = $this->gameMessages[0];
            $this->showMessageModal = true;

            return;
        }

        // Очередь исчерпана: закрываем, чистим сессию (по спецификации),
        // переводим игрока на экран 11 «Решение принято»
        $this->showMessageModal = false;
        $this->messageShown = true;
        $this->currentModalMessage = [];
        $this->gameMessages = [];
        $this->effectManager->clearMessages();

        $this->gameplayTab = 'decisions';
        $this->decisionsDoc = 'result';
    }

    private function applyGameResult(array $result): void
    {
        Log::info('applyGameResult начат', [
            'game_id' => $this->game->id,
            'next_scene_id' => $result['next_scene_id'] ?? null,
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
        $this->resetDecisionState();
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
        // Просто возвращаем то, что отдал Accessor модели Scene
        return $this->scene->additional_data;
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
    public function openFolder(): void
    {
        $this->phase = 'introduction';
    }

    /**
     * Принять дела: переход с «Введения» в игру
     */
    public function acceptDuties(): void
    {
        $this->phase = 'gameplay';
    }

    /**
     * Вернуться к выбору сценариев
     */
    public function backToScenarios(): void
    {
        redirect()->route('scenarios');
    }

    /**
     * Загрузить название и описание сценария для экрана «Закрытая папка»
     */
    private function loadScenarioInfo(): void
    {
        $scenario = Scenario::whereHas('scenes', function ($query) {
            $query->where('id', $this->game->current_scene_id);
        })->first();

        $this->scenarioInfo = [
            'name'        => $scenario->name ?? 'Неизвестный сценарий',
            'description' => $scenario->description ?? '',
        ];
    }

    /**
     * Подпись сложности по макету UI V1
     */
    public function getDifficultyLabel(string $difficulty): string
    {
        return self::DIFFICULTIES[$difficulty]['label']
            ?? ($difficulty === 'custom' ? 'Пользовательский' : $difficulty);
    }

    public function getDifficultyDescription(string $difficulty): string
    {
        return self::DIFFICULTIES[$difficulty]['description'] ?? '';
    }
    /**
     * Переключение вкладки игрового досье
     */
    public function setGameplayTab(string $tab): void
    {
        $this->gameplayTab = $tab;

        if ($tab === 'decisions') {
            $this->decisionsDoc = 'decisions';
        }

        // Таймер решений стартует при первом входе на вкладку «Решения» (экран 05, §13)
        if ($tab === 'decisions' && $this->scene && !$this->decisionDeadline && !$this->decisionExpired) {
            $this->timeLimit = $this->gameService->getTimeLimit(
                $this->scene->id,
                $this->game->difficulty,
                Auth::id()
            );
            $this->decisionDeadline = now()->addSeconds($this->timeLimit)->getTimestamp();
        }
    }
    /**
     * Тон метрики для стилей макета (success/neutral/warning/danger)
     */
    public function getMetricTone(int $value): string
    {
        return match (true) {
            $value >= 80 => 'success',
            $value >= 60 => 'neutral',
            $value >= 20 => 'warning',
            default => 'danger',
        };
    }

    /**
     * Иконка актора по типу (для панели «Сообщения от акторов»)
     */
    public function getActorIcon(string $name): string
    {
        return match (true) {
            str_contains($name, 'Институциональ') => 'building',
            str_contains($name, 'Аппарат') => 'briefcase',
            str_contains($name, 'Экономич') => 'chart',
            str_contains($name, 'Политич') => 'flag',
            str_contains($name, 'Социаль') => 'group',
            str_contains($name, 'Медий') => 'broadcast',
            default => 'briefcase',
        };
    }
    /**
     * Выбор варианта решения (без применения)
     */
    public function selectOption(int $choiceId): void
    {
        if ($this->decisionExpired) {
            return;
        }

        if (!collect($this->getVisibleChoices())->firstWhere('id', $choiceId)) {
            return;
        }

        $this->selectedChoiceId = $choiceId;
    }

    /**
     * Подтвердить выбранное решение (запускает существующий поток selectChoice)
     */
    public function confirmDecision(): void
    {
        if (!$this->selectedChoiceId || $this->decisionExpired) {
            return;
        }

        $this->selectChoice($this->selectedChoiceId);
    }

    /**
     * Видимые варианты решения: скрытый исход «Ничего не делать» не показывается (§12)
     */
    public function getVisibleChoices(): array
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        return collect($this->availableChoices)
            ->filter(fn ($choice) => !str_contains(mb_strtolower($choice->description), 'ничего не делать'))
            ->values()
            ->map(fn ($choice, $index) => [
                'id' => $choice->id,
                'letter' => $letters[$index] ?? (string) ($index + 1),
                'title' => $choice->description,
            ])
            ->toArray();
    }

    /**
     * Сброс состояния решений при смене сцены
     */
    private function resetDecisionState(): void
    {
        $this->selectedChoiceId = null;
        $this->decisionDeadline = null;
        $this->decisionExpired = false;
        $this->gameplayTab = 'scenario';
    }
    /**
     * Переключение вида каталога (карточки/список)
     */
    public function setMaterialView(string $view): void
    {
        $this->materialView = $view === 'list' ? 'list' : 'cards';
    }

    /**
     * Открыть предпросмотр материала
     */
    public function openMaterial(string $materialId): void
    {
        if (!collect(PrototypeContent::materials())->firstWhere('id', $materialId)) {
            return;
        }

        $this->activeMaterialId = $materialId;
    }

    public function closeMaterialModal(): void
    {
        $this->activeMaterialId = null;
    }

    /**
     * Отметить материал как изученный и закрыть предпросмотр
     */
    public function markMaterialStudied(): void
    {
        if ($this->activeMaterialId && !in_array($this->activeMaterialId, $this->studiedMaterialIds, true)) {
            $this->studiedMaterialIds[] = $this->activeMaterialId;
        }

        $this->activeMaterialId = null;
    }

    /**
     * Видимые материалы с учётом категории и сортировки (источник — PrototypeContent)
     */
    public function getVisibleMaterials(): array
    {
        $items = collect(PrototypeContent::materials());

        if ($this->materialCategory !== 'all') {
            $items = $items->filter(fn ($material) => $material['category'] === $this->materialCategory);
        }

        return match ($this->materialSort) {
            'date' => $items->sortByDesc('date')->values()->toArray(),
            'type' => $items->sortBy('categoryLabel')->values()->toArray(),
            default => $items->sortByDesc('priority')->values()->toArray(),
        };
    }

    /**
     * Материал для модального окна предпросмотра
     */
    public function getActiveMaterial(): ?array
    {
        if (!$this->activeMaterialId) {
            return null;
        }

        return collect(PrototypeContent::materials())->firstWhere('id', $this->activeMaterialId);
    }
    /**
     * Переключение вида каталога справок (карточки/список)
     */
    public function setReferenceView(string $view): void
    {
        $this->referenceView = $view === 'list' ? 'list' : 'cards';
    }

    /**
     * Открыть просмотр справки
     */
    public function openReference(string $referenceId): void
    {
        if (!collect(PrototypeContent::references())->firstWhere('id', $referenceId)) {
            return;
        }

        $this->activeReferenceId = $referenceId;
    }

    public function closeReferenceModal(): void
    {
        $this->activeReferenceId = null;
    }

    /**
     * Отметить справку как изученную и закрыть просмотр
     */
    public function markReferenceStudied(): void
    {
        if ($this->activeReferenceId && !in_array($this->activeReferenceId, $this->studiedReferenceIds, true)) {
            $this->studiedReferenceIds[] = $this->activeReferenceId;
        }

        $this->activeReferenceId = null;
    }

    /**
     * Видимые справки с учётом категории и сортировки (источник — PrototypeContent)
     */
    public function getVisibleReferences(): array
    {
        $items = collect(PrototypeContent::references());

        if ($this->referenceCategory !== 'all') {
            $items = $items->filter(fn ($reference) => $reference['category'] === $this->referenceCategory);
        }

        return match ($this->referenceSort) {
            'date' => $items->sortByDesc('date')->values()->toArray(),
            'type' => $items->sortBy('categoryLabel')->values()->toArray(),
            default => $items->sortByDesc('priority')->values()->toArray(),
        };
    }

    /**
     * Справка для модального окна просмотра
     */
    public function getActiveReference(): ?array
    {
        if (!$this->activeReferenceId) {
            return null;
        }

        return collect(PrototypeContent::references())->firstWhere('id', $this->activeReferenceId);
    }
    /**
     * Переключение вида каталога СМИ (карточки/список)
     */
    public function setMediaView(string $view): void
    {
        $this->mediaView = $view === 'list' ? 'list' : 'cards';
    }

    /**
     * Открыть просмотр материала СМИ
     */
    public function openMedia(string $mediaId): void
    {
        if (!collect(PrototypeContent::media())->firstWhere('id', $mediaId)) {
            return;
        }

        $this->activeMediaId = $mediaId;
    }

    public function closeMediaModal(): void
    {
        $this->activeMediaId = null;
    }

    /**
     * Отметить материал СМИ как изученный и закрыть просмотр
     */
    public function markMediaReviewed(): void
    {
        if ($this->activeMediaId && !in_array($this->activeMediaId, $this->reviewedMediaIds, true)) {
            $this->reviewedMediaIds[] = $this->activeMediaId;
        }

        $this->activeMediaId = null;
    }

    /**
     * Видимые материалы СМИ с учётом категории и сортировки (источник — PrototypeContent)
     */
    public function getVisibleMedia(): array
    {
        $items = collect(PrototypeContent::media());

        if ($this->mediaCategory !== 'all') {
            $items = $items->filter(fn ($item) => $item['category'] === $this->mediaCategory);
        }

        return match ($this->mediaSort) {
            'time' => $items->sortByDesc('dateTime')->values()->toArray(),
            'resonance' => $items->sortByDesc(fn ($item) => [$item['resonanceValue'], $item['priorityValue']])->values()->toArray(),
            default => $items->sortByDesc('priorityValue')->values()->toArray(),
        };
    }

    /**
     * Материал СМИ для модального окна просмотра
     */
    public function getActiveMedia(): ?array
    {
        if (!$this->activeMediaId) {
            return null;
        }

        return collect(PrototypeContent::media())->firstWhere('id', $this->activeMediaId);
    }
    /**
     * Переключение вида каталога обращений (карточки/список)
     */
    public function setAppealView(string $view): void
    {
        $this->appealView = $view === 'list' ? 'list' : 'cards';
    }

    /**
     * Открыть карточку обращения
     */
    public function openAppeal(string $appealId): void
    {
        if (!collect(PrototypeContent::appeals())->firstWhere('id', $appealId)) {
            return;
        }

        $this->activeAppealId = $appealId;
    }

    public function closeAppealModal(): void
    {
        $this->activeAppealId = null;
    }

    /**
     * Отметить обращение как изученное и закрыть карточку
     */
    public function markAppealReviewed(): void
    {
        if ($this->activeAppealId && !in_array($this->activeAppealId, $this->reviewedAppealIds, true)) {
            $this->reviewedAppealIds[] = $this->activeAppealId;
        }

        $this->activeAppealId = null;
    }

    /**
     * Видимые обращения с учётом категории и сортировки (источник — PrototypeContent)
     */
    public function getVisibleAppeals(): array
    {
        $items = collect(PrototypeContent::appeals());

        if ($this->appealCategory !== 'all') {
            $items = $items->filter(fn ($appeal) => $appeal['category'] === $this->appealCategory);
        }

        return match ($this->appealSort) {
            'time' => $items->sortByDesc('dateTime')->values()->toArray(),
            'repeat' => $items->sortByDesc(fn ($appeal) => [$appeal['repeatCount'], $appeal['priorityValue']])->values()->toArray(),
            default => $items->sortByDesc(fn ($appeal) => [$appeal['priorityValue'], $appeal['dateTime']])->values()->toArray(),
        };
    }

    /**
     * Обращение для модального окна карточки
     */
    public function getActiveAppeal(): ?array
    {
        if (!$this->activeAppealId) {
            return null;
        }

        return collect(PrototypeContent::appeals())->firstWhere('id', $this->activeAppealId);
    }
    /**
     * Открыть входящие (экран 12)
     */
    public function openInbox(): void
    {
        $this->decisionsDoc = 'inbox';
        $this->gameplayTab = 'decisions';
    }

    /**
     * Продолжить после результата: возврат к сцене; при наличии — модалка отложенного последствия (экран 13)
     */
    public function continueFromResult(): void
    {
        $this->decisionsDoc = 'decisions';
        $this->gameplayTab = 'scenario';
        if ($this->hasDelayedMessages) {
            $this->showDelayedModal = true;
        }
    }

    public function backToResult(): void
    {
        $this->decisionsDoc = 'result';
    }

    /**
     * Открыть письмо из входящих (модалка экрана 10) и пометить прочитанным
     */
    public function selectInboxMessage(int $messageId): void
    {
        $message = collect($this->inboxMessages)->firstWhere('id', $messageId);
        if (!$message) {
            return;
        }
        $this->currentModalMessage = $message;
        $this->showMessageModal = true;
        $this->messageModalFromInbox = true;
        if (!in_array($messageId, $this->readInboxIds, true)) {
            $this->readInboxIds[] = $messageId;
        }
    }

    public function markAllInboxRead(): void
    {
        $this->readInboxIds = collect($this->inboxMessages)->pluck('id')->all();
    }
    public function resetInboxFilters(): void
    {
        $this->inboxFilter = 'all';
        $this->inboxActorFilter = 'all';
        $this->inboxSort = 'unread-first';
    }


    public function isInboxRead(int $messageId): bool
    {
        return in_array($messageId, $this->readInboxIds, true);
    }

    /**
     * Видимые сообщения входящих с учётом фильтров (экран 12)
     */
    public function getVisibleInboxMessages(): array
    {
        return collect($this->inboxMessages)
            ->filter(function ($message) {
                $read = $this->isInboxRead($message['id']);
                $statusOk = $this->inboxFilter === 'all'
                    || ($this->inboxFilter === 'new' && !$read)
                    || ($this->inboxFilter === 'urgent' && $message['urgent'])
                    || ($this->inboxFilter === 'read' && $read);
                $actorOk = $this->inboxActorFilter === 'all' || $message['actor'] === $this->inboxActorFilter;
                return $statusOk && $actorOk;
            })
            ->sortBy(fn ($message) => match ($this->inboxSort) {
                'priority' => [-$this->priorityRank($message['priority']), $message['id']],
                'new-first' => [$message['id']],
                default => [(int) $this->isInboxRead($message['id']), -(int) $message['urgent'], -$this->priorityRank($message['priority']), $message['id']],
            })
            ->values()
            ->toArray();
    }

    /**
     * Сводка входящих (экран 12)
     */
    public function getInboxSummary(): array
    {
        $messages = collect($this->inboxMessages);
        return [
            'total' => $messages->count(),
            'unread' => $messages->filter(fn ($m) => !$this->isInboxRead($m['id']))->count(),
            'urgent' => $messages->filter(fn ($m) => $m['urgent'])->count(),
            'actors' => $messages->pluck('actor')->unique()->count(),
        ];
    }

    /**
     * Нормализация сообщений движка к структуре входящих (экран 12)
     */
    private function normalizeMessages(array $messages): array
    {
        return collect($messages)->values()->map(function ($message, $index) {
            $text = $message['message'] ?? $message['text'] ?? '';
            $actor = $message['actor'] ?? $message['type'] ?? 'Актор';
            return [
                'id' => $index,
                'actor' => $actor,
                'sender' => $message['sender'] ?? $message['source'] ?? $actor,
                'subject' => $message['subject'] ?? (mb_strlen($text) > 60 ? mb_substr($text, 0, 60) . '…' : $text),
                'preview' => mb_strlen($text) > 120 ? mb_substr($text, 0, 120) . '…' : $text,
                'priority' => $message['priority'] ?? 'Средний',
                'urgent' => (bool) ($message['urgent'] ?? false),
                'time' => $message['created_at'] ?? $message['time'] ?? now()->format('d.m.Y H:i'),
                'text' => $text,
            ];
        })->toArray();
    }

    private function priorityRank(string $priority): int
    {
        return match (true) {
            str_contains($priority, 'Критич') => 4,
            str_contains($priority, 'Высок') => 3,
            str_contains($priority, 'Средн') => 2,
            default => 1,
        };
    }

    public function render()
    {
        return view('livewire.game-play')
            ->layout('layouts.game')
            ->title('Игра');
    }
}
