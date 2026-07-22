<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Scene;
use App\Models\Choice;
use App\Models\Event;
use App\Models\Preset;
use App\Models\GameHistory;
use App\Models\EffectType;
use App\Services\Effects\EffectManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameService
{
    const CACHE_TTL = 3600; // 1 час

    private EffectManager $effectManager;

    public function __construct()
    {
        $this->effectManager = new EffectManager();
    }

    /**
     * Старт новой игры
     */
    public function startGame(int $userId, int $scenarioId, string $difficulty): Game
    {
        // Получаем пресет для сценария и сложности
        $preset = Preset::where('scenario_id', $scenarioId)
            ->where('difficulty', $difficulty)
            ->first();

        if (!$preset) {
            throw new \Exception("Пресет для сценария {$scenarioId} и сложности {$difficulty} не найден");
        }

        // Находим первую сцену (минимальный order)
        $firstScene = Scene::where('scenario_id', $scenarioId)
            ->orderBy('order', 'asc')
            ->first();

        if (!$firstScene) {
            throw new \Exception("Для сценария {$scenarioId} не найдено ни одной сцены");
        }

        Log::info('Старт игры', [
            'scenario_id' => $scenarioId,
            'first_scene_id' => $firstScene->id,
            'first_scene_order' => $firstScene->order,
            'first_scene_title' => $firstScene->title,
        ]);

        // Получаем company_id
        $companyId = $this->getCompanyId($scenarioId);

        // Создаём игру
        $game = Game::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'current_scene_id' => $firstScene->id,
            'difficulty' => $difficulty,
            'status' => 'in_progress',
        ]);

        // Кэшируем игру
        $this->cacheGame($game);

        return $game;
    }

    /**
     * Получить ID компании для сценария
     */
    private function getCompanyId(int $scenarioId): int
    {
        $company = \App\Models\Company::whereHas('scenarios', function ($query) use ($scenarioId) {
            $query->where('scenario_id', $scenarioId);
        })->first();

        if ($company) {
            return $company->id;
        }

        $company = \App\Models\Company::first();
        if ($company) {
            return $company->id;
        }

        $company = \App\Models\Company::create([
            'name' => 'Администрация округа',
            'description' => 'Компания по умолчанию',
            'difficulty' => 'easy',
        ]);

        return $company->id;
    }

    /**
     * Сделать выбор
     */
    public function makeChoice(int $gameId, int $choiceId): array
    {
        Log::info('makeChoice начат', ['game_id' => $gameId, 'choice_id' => $choiceId]);

        // Получаем игру из кэша или БД
        $game = $this->getGame($gameId);

        // Получаем выбор
        $choice = Choice::with('event.effects.effectType')->find($choiceId);

        if (!$choice) {
            throw new \Exception("Выбор с ID {$choiceId} не найден");
        }

        Log::info('Выбор найден', [
            'choice_id' => $choice->id,
            'event_id' => $choice->event_id,
            'event_name' => $choice->event->name ?? null,
        ]);

        // Получаем текущее состояние
        $currentState = $game->getCurrentState();

        // Применяем эффекты события выбора
        $newState = $this->applyEventEffects($game, $currentState, $choice->event);

        // Записываем в историю
        GameHistory::create([
            'game_id' => $gameId,
            'event_id' => $choice->event_id,
        ]);

        // Проверяем триггеры акторов
        $triggeredEvents = $this->processActorTriggers($game, $newState);

        // Применяем события от акторов
        foreach ($triggeredEvents as $trigger) {
            $event = Event::with('effects.effectType')->find($trigger['event_id']);
            if ($event) {
                $newState = $this->applyEventEffects($game, $newState, $event);

                GameHistory::create([
                    'game_id' => $gameId,
                    'event_id' => $event->id,
                ]);
            }
        }

        // Определяем следующую сцену (с учётом эффекта "Смена сцены")
        $nextScene = $this->determineNextScene($game, $choice->event);

        Log::info('Определена следующая сцена', [
            'has_next_scene' => $nextScene ? 'да' : 'нет',
            'next_scene_id' => $nextScene->id ?? null,
            'next_scene_title' => $nextScene->title ?? null,
            'next_scene_order' => $nextScene->order ?? null,
        ]);

        // Если следующей сцены нет - игра завершена
        if (!$nextScene) {
            $game->update([
                'status' => 'completed',
                'current_scene_id' => null,
            ]);
            Log::info('Игра завершена', ['game_id' => $gameId]);
        } else {
            $game->update([
                'current_scene_id' => $nextScene->id,
            ]);
            Log::info('Обновлена текущая сцена', [
                'game_id' => $gameId,
                'new_scene_id' => $nextScene->id,
                'new_scene_title' => $nextScene->title,
            ]);
        }

        // Обновляем кэш
        $this->cacheGame($game);
        Cache::forget("game_state_{$gameId}");

        // Возвращаем результат
        return [
            'game' => $game,
            'choice' => $choice,
            'new_state' => $newState,
            'triggered_events' => $triggeredEvents,
            'next_scene' => $nextScene,
        ];
    }

    /**
     * Применить эффекты события к состоянию
     */
    private function applyEventEffects(Game $game, array $state, Event $event): array
    {
        foreach ($event->effects as $effect) {
            $state = $this->applyEffect($state, $effect);
        }
        return $state;
    }

    private function applyEffect(array $state, $effect): array
    {
        // Данные уже массив благодаря касту в модели Effect
        $data = $effect->effect_data;

        // Если вдруг строка — декодируем
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
        }

        if (!is_array($data)) {
            return $state;
        }

        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            return $state;
        }

        $numericValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        $operation = str_starts_with($value, '+') ? '+' :
            (str_starts_with($value, '-') ? '-' : '=');

        if (!isset($state[$key])) {
            $state[$key] = 0;
        }

        switch ($operation) {
            case '+':
                $state[$key] += $numericValue;
                break;
            case '-':
                $state[$key] -= $numericValue;
                break;
            case '=':
                $state[$key] = $numericValue;
                break;
        }

        $state[$key] = max(0, min(100, $state[$key]));

        return $state;
    }

    /**
     * Обработать триггеры акторов
     * Каждое уникальное событие актора срабатывает только один раз за ход
     */
    private function processActorTriggers(Game $game, array $currentState): array
    {
        $triggeredEvents = [];
        $processedEvents = []; // Для отслеживания уже обработанных событий
        $maxIterations = 5;
        $iteration = 0;

        $actors = $this->getActors();

        do {
            $foundTrigger = false;
            $iteration++;

            foreach ($actors as $actor) {
                $triggers = $actor->triggers;

                if (is_string($triggers)) {
                    $triggers = json_decode($triggers, true);
                }
                if (is_string($triggers)) {
                    $triggers = json_decode($triggers, true);
                }

                if (!is_array($triggers)) {
                    continue;
                }

                foreach ($triggers as $trigger) {
                    $key = $trigger['key'] ?? null;
                    $value = $trigger['value'] ?? null;
                    $eventId = $trigger['event_id'] ?? null;

                    if (!$key || !$value || !$eventId) {
                        continue;
                    }

                    // Проверяем, не было ли уже это событие в текущем ходе
                    $eventKey = $actor->id . '_' . $eventId;
                    if (isset($processedEvents[$eventKey])) {
                        continue; // Пропускаем, если уже обработано
                    }

                    if ($this->checkTriggerCondition($currentState, $key, $value)) {
                        $event = Event::with('effects.effectType')->find($eventId);
                        if ($event) {
                            // Применяем эффекты события
                            $currentState = $this->applyEventEffects($game, $currentState, $event);

                            // Отмечаем событие как обработанное
                            $processedEvents[$eventKey] = true;

                            // Собираем сообщения из эффектов события
                            $messages = [];
                            foreach ($event->effects as $effect) {
                                $data = json_decode($effect->effect_data, true);
                                if (isset($data['message']) && !empty($data['message'])) {
                                    $messages[] = $data['message'];
                                }
                            }

                            $triggeredEvents[] = [
                                'actor_id' => $actor->id,
                                'actor_name' => $actor->name,
                                'event_id' => $eventId,
                                'event_name' => $event->name,
                                'event_description' => $event->description,
                                'messages' => $messages,
                                'trigger_key' => $key,
                                'trigger_value' => $value,
                            ];

                            $foundTrigger = true;
                            break 2;
                        }
                    }
                }
            }

        } while ($foundTrigger && $iteration < $maxIterations);

        return $triggeredEvents;
    }

    /**
     * Проверить условие триггера
     */
    private function checkTriggerCondition(array $state, string $key, string $value): bool
    {
        // Ищем значение в состоянии
        $currentValue = null;

        // 1. Проверяем прямой доступ по ключу
        if (isset($state[$key])) {
            $currentValue = $state[$key];
        }

        // 2. Если не нашли, ищем в числовых индексах
        if ($currentValue === null) {
            foreach ($state as $item) {
                if (is_array($item) && isset($item['key']) && $item['key'] === $key) {
                    $currentValue = $item['value'];
                    break;
                }
            }
        }

        // 3. Если всё ещё не нашли, ищем в любых вложенных массивах
        if ($currentValue === null) {
            foreach ($state as $item) {
                if (is_array($item)) {
                    foreach ($item as $subKey => $subValue) {
                        if ($subKey === $key) {
                            $currentValue = $subValue;
                            break 2;
                        }
                    }
                }
            }
        }

        // Если значение не найдено - триггер не срабатывает
        if ($currentValue === null) {
            return false;
        }

        // Приводим к числу для сравнения
        $currentValue = (int) $currentValue;

        // Парсим диапазон, например "0-20", "21-40", "81-100"
        if (strpos($value, '-') !== false) {
            [$min, $max] = explode('-', $value);
            return $currentValue >= (int) $min && $currentValue <= (int) $max;
        }

        // Парсим операторы сравнения, например ">50", "<20", "=100"
        if (str_starts_with($value, '>')) {
            return $currentValue > (int) substr($value, 1);
        }
        if (str_starts_with($value, '<')) {
            return $currentValue < (int) substr($value, 1);
        }
        if (str_starts_with($value, '=')) {
            return $currentValue == (int) substr($value, 1);
        }

        // Точное совпадение
        return $currentValue == (int) $value;
    }

    /**
     * Определить следующую сцену
     */
    private function determineNextScene(Game $game, Event $event): ?Scene
    {
        Log::info('determineNextScene начат', [
            'game_id' => $game->id,
            'current_scene_id' => $game->current_scene_id,
            'event_id' => $event->id,
            'event_name' => $event->name,
        ]);

        // Проверяем принудительный переход из эффекта
        $forced = $this->effectManager->getForcedScene();
        if ($forced && isset($forced['scene_id'])) {
            $scene = Scene::find($forced['scene_id']);
            if ($scene) {
                Log::info('Принудительный переход на сцену', [
                    'scene_id' => $scene->id,
                    'scene_title' => $scene->title,
                ]);
                return $scene;
            }
        }

        // Ищем эффект типа "Смена сцены" в событии
        $sceneTransitionEffect = $event->effects->first(function ($effect) {
            return $effect->effectType && $effect->effectType->name === 'Смена сцены';
        });

        if ($sceneTransitionEffect) {
            $data = $sceneTransitionEffect->effect_data;

            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            $targetSceneId = $data['target_scene_id'] ?? null;

            Log::info('Найден эффект "Смена сцены"', [
                'target_scene_id' => $targetSceneId,
                'data' => $data,
            ]);

            if ($targetSceneId) {
                $targetScene = Scene::find($targetSceneId);
                if ($targetScene) {
                    Log::info('Целевая сцена найдена', [
                        'id' => $targetScene->id,
                        'title' => $targetScene->title,
                    ]);
                    return $targetScene;
                }
            }
        }

        // Если нет эффекта смены сцены - берем следующую по порядку
        $nextScene = $game->getNextScene();

        Log::info('Следующая сцена по порядку', [
            'next_scene_id' => $nextScene->id ?? null,
            'next_scene_title' => $nextScene->title ?? null,
            'next_scene_order' => $nextScene->order ?? null,
        ]);

        return $nextScene;
    }

    /**
     * Получить игру (из кэша или БД)
     */
    public function getGame(int $gameId): Game
    {
        $cacheKey = $this->getCacheKey($gameId);
        $cachedGameId = Cache::get($cacheKey);

        if ($cachedGameId) {
            $game = Game::with([
                'currentScene' => function ($query) {
                    $query->with(['choices' => function ($q) {
                        $q->with('event.effects.effectType');
                    }]);
                },
                'gameHistories' => function ($query) {
                    $query->with('event.effects.effectType');
                }
            ])->find($cachedGameId);

            if ($game) {
                return $game;
            }
        }

        $game = Game::with([
            'currentScene' => function ($query) {
                $query->with(['choices' => function ($q) {
                    $q->with('event.effects.effectType');
                }]);
            },
            'gameHistories' => function ($query) {
                $query->with('event.effects.effectType');
            }
        ])->find($gameId);

        if (!$game) {
            throw new \Exception("Игра с ID {$gameId} не найдена");
        }

        $this->cacheGame($game);

        return $game;
    }

    /**
     * Получить текущую сцену для игры
     */
    public function getCurrentScene(Game $game): ?Scene
    {
        return $game->currentScene;
    }

    /**
     * Получить доступные выборы для текущей сцены
     */
    public function getAvailableChoices(Game $game): array
    {
        $scene = $this->getCurrentScene($game);

        if (!$scene) {
            return [];
        }

        $state = $game->getCurrentState();
        $choices = $scene->choices;
        $availableChoices = [];

        foreach ($choices as $choice) {
            // Если есть условия - проверяем их
            if ($choice->conditions) {
                $conditions = $choice->conditions;

                // Если условия - строка JSON, декодируем
                if (is_string($conditions)) {
                    $conditions = json_decode($conditions, true);
                }

                // Если после декодирования всё ещё строка (двойное экранирование)
                if (is_string($conditions)) {
                    $conditions = json_decode($conditions, true);
                }

                if (is_array($conditions) && $this->checkChoiceConditions($conditions, $state)) {
                    $availableChoices[] = $choice;
                }
            } else {
                $availableChoices[] = $choice;
            }
        }

        return $availableChoices;
    }

    /**
     * Проверить условия выбора
     */
    private function checkChoiceConditions(array $conditions, array $state): bool
    {
        foreach ($conditions as $condition) {
            $key = $condition['key'] ?? null;
            $value = $condition['value'] ?? null;

            if (!$key || !$value) {
                continue;
            }

            // Ищем значение в состоянии (аналогично checkTriggerCondition)
            $currentValue = null;

            if (isset($state[$key])) {
                $currentValue = $state[$key];
            }

            if ($currentValue === null) {
                foreach ($state as $item) {
                    if (is_array($item) && isset($item['key']) && $item['key'] === $key) {
                        $currentValue = $item['value'];
                        break;
                    }
                }
            }

            if ($currentValue === null) {
                return false;
            }

            if (!$this->checkTriggerCondition($state, $key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Кэшировать игру (сохраняем только ID)
     */
    private function cacheGame(Game $game): void
    {
        $cacheKey = $this->getCacheKey($game->id);
        Cache::put($cacheKey, $game->id, self::CACHE_TTL);
    }

    /**
     * Получить ключ кэша для игры
     */
    private function getCacheKey(int $gameId): string
    {
        return "game_{$gameId}";
    }

    /**
     * Очистить кэш игры
     */
    public function clearCache(int $gameId): void
    {
        Cache::forget($this->getCacheKey($gameId));
        Cache::forget("game_state_{$gameId}");
    }

    /**
     * Получить всех акторов
     */
    private function getActors(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Actor::all();
    }
    public function getTimeLimit(int $sceneId, string $difficulty, int $userId): int
    {
        $scene = Scene::find($sceneId);
        if (!$scene) {
            return 60;
        }

        $baseTime = $scene->time_limit ?? 60;

        $multipliers = [
            'easy' => 1.0,
            'medium' => 0.75,
            'hard' => 0.5,
            'expert' => 0.25,
            'custom' => 0.75,
        ];

        $time = $baseTime * ($multipliers[$difficulty] ?? 1.0);

        $completedGamesCount = Game::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        // Если за опыт отнимается время
        $penalty = min(floor($completedGamesCount / 5) * 0.1, 0.5);
        $time = $time * (1 - $penalty); // ВНИМАНИЕ: минус!

        return (int) ceil($time);
    }
}
