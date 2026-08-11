<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Preset;
use App\Models\Choice;
use App\Models\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\GameHistory;
use App\Support\PrototypeContent;
use App\Models\ParameterDefinition;
class GameResults extends Component
{
    public Game $game;
    public array $statistics = [];
    public array $finalState = [];
    public array $initialState = [];
    public array $parametersWithDiff = [];
    public array $historyData = [];
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
    public string $scenarioName = '';
    public string $difficulty = '';
    public int $totalSteps = 0;

    /** Активная годовая вкладка (экраны 14–16) */
    public string $annualTab = 'year-results';

    /** Состояние хроники года (экран 16) */
    public string $chronicleFilter = 'all';
    public string $chronicleActorFilter = 'all';
    public string $chronicleSort = 'chronological';
    public array $expandedChronicleIds = ['january-first-signal'];
    public ?string $highlightedChronicleId = null;
    public ?string $chronicleTargetId = null;

    /** Подтверждение переигровки */
    public bool $replayConfirmationOpen = false;

    /** ID сценария (для переигровки) */
    public int $scenarioId = 0;
    public function mount(int $gameId): void
    {
        $this->game = Game::with([
            'user',
            'currentScene',
            'gameHistories.event.effects.effectType',
            'gameHistories.event.choices'
        ])->find($gameId);

        if (!$this->game || $this->game->user_id !== Auth::id()) {
            abort(404, 'Игра  не найдена');
        }

        $this->loadStatistics();
        $this->loadHistoryData();
    }

    private function loadStatistics(): void
    {
        $this->initialState = $this->getInitialState();

        $finalState = $this->game->getCurrentState(true);
        if (empty($finalState)) {
            $finalState = $this->buildStateFromHistory();
        }
        $this->finalState = $finalState;

        $this->parametersWithDiff = $this->calculateDiff();

        // ========== ПОДСЧЕТ УНИКАЛЬНЫХ СЦЕН ==========
        $sceneIds = [];
        $histories = $this->game->gameHistories()->with('scene')->get();

        foreach ($histories as $history) {
            if ($history->scene_id && !in_array($history->scene_id, $sceneIds)) {
                $sceneIds[] = $history->scene_id;
            }
        }
        $this->totalSteps = count($sceneIds);

        // Определяем имя сценария
        $firstHistory = $this->game->gameHistories()->with('event')->first();
        $scenarioName = 'Неизвестный сценарий';

        if ($firstHistory && $firstHistory->event) {
            $choice = Choice::where('event_id', $firstHistory->event_id)->first();
            if ($choice && $choice->scene) {
                $scenario = $choice->scene->scenario;
                if ($scenario) {
                    $scenarioName = $scenario->name;
                    $this->scenarioId = $scenario->id;
                }
            }
        }

        if ($scenarioName === 'Неизвестный сценарий') {
            $anyChoice = Choice::with('scene.scenario')->first();
            if ($anyChoice && $anyChoice->scene && $anyChoice->scene->scenario) {
                $scenarioName = $anyChoice->scene->scenario->name;
            }
        }

        $this->scenarioName = $scenarioName;
        $this->difficulty = $this->game->difficulty;

        $this->statistics = [
            'total_steps' => $this->totalSteps,
            'difficulty' => $this->difficulty,
            'scenario_name' => $this->scenarioName,
            'finished_at' => $this->game->updated_at->format('d.m.Y H:i'),
        ];
    }

    private function loadHistoryData(): void
    {
        $histories = $this->game->gameHistories()
            ->with(['event.effects.effectType', 'event.choices.scene', 'scene'])  // <-- Добавляем 'scene'
            ->orderBy('id', 'asc')
            ->get();

        if ($histories->isEmpty()) {
            $this->historyData = [];
            return;
        }

        // ========== ГРУППИРУЕМ ПО ШАГАМ ==========
        $steps = [];
        $currentStepEvents = [];
        $hasPlayerEventInStep = false;

        foreach ($histories as $history) {
            $event = $history->event;
            if (!$event) continue;

            // Пропускаем системные события
            if ($history->source === GameHistory::SOURCE_SYSTEM) {
                continue;
            }

            if ($history->source === GameHistory::SOURCE_PLAYER) {
                if ($hasPlayerEventInStep && !empty($currentStepEvents)) {
                    $steps[] = $this->buildStepData($currentStepEvents);
                    $currentStepEvents = [];
                    $hasPlayerEventInStep = false;
                }

                $currentStepEvents[] = $history;
                $hasPlayerEventInStep = true;
            } else {
                // source === 'actor'
                if ($hasPlayerEventInStep) {
                    $currentStepEvents[] = $history;
                }
            }
        }

        if (!empty($currentStepEvents)) {
            $steps[] = $this->buildStepData($currentStepEvents);
        }

        $this->historyData = $steps;
    }

    private function buildStepData(array $stepEvents): array
    {
        $choiceDescription = '';
        $effectsMap = [];
        $actorReactions = [];
        $sceneOrder = 0;
        $sceneTitle = '';
        $situation = '';
        $hasDelayedEffect = false;
        $delayedText = '';
        $date = now()->format('d.m.Y H:i');

        // Находим игровой ход в этом шаге
        $playerHistory = null;
        foreach ($stepEvents as $history) {
            if ($history->source === GameHistory::SOURCE_PLAYER) {
                $playerHistory = $history;
                break;
            }
        }

        // ========== ЕСЛИ ЕСТЬ ИГРОВОЙ ХОД - БЕРЕМ СЦЕНУ ИЗ НЕГО ==========
        if ($playerHistory) {
            $event = $playerHistory->event;

            // Берем сцену из записи истории (теперь она там есть!)
            if ($playerHistory->scene_id) {
                $scene = $playerHistory->scene;
                if ($scene) {
                    $sceneOrder = $scene->order;
                    $sceneTitle = $scene->title;
                    $situation = $scene->situation ?? '';
                }
            }

            // Берем описание выбора
            $choice = $event->choices->first();
            if ($choice) {
                $choiceDescription = $choice->description;
            }

            $date = $playerHistory->created_at->format('d.m.Y H:i');
        }

        // ========== РЕАКЦИИ АКТОРОВ ==========
        foreach ($stepEvents as $history) {
            $event = $history->event;
            if (!$event) continue;

            if ($history->source === GameHistory::SOURCE_ACTOR) {
                $actorName = $this->extractActorName($event->name);
                $eventAction = $this->extractActorAction($event->name);

                if (!empty($actorName) && $actorName !== 'Актор') {
                    $exists = false;
                    foreach ($actorReactions as $reaction) {
                        if ($reaction['actor'] === $actorName && $reaction['event'] === $eventAction) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $actorReactions[] = [
                            'actor' => $actorName,
                            'event' => $eventAction,
                        ];
                    }
                }
            }

            // ========== ЭФФЕКТЫ ==========
            foreach ($event->effects as $effect) {
                $typeName = $effect->effectType->name ?? '';
                if ($typeName === 'Отложенное сообщение') {
                    $d = $effect->effect_data;
                    if (is_string($d)) $d = json_decode($d, true);
                    if (is_string($d)) $d = json_decode($d, true);
                    $hasDelayedEffect = true;
                    if (!empty($d['message'])) $delayedText = $d['message'];
                }

                $parsed = $this->parseEffect($effect);
                if ($parsed['key'] === 'unknown') continue;

                $key = $parsed['key'];

                // Пропускаем системные эффекты
                if (strpos($key, 'сила реакции') !== false || strpos($key, 'Сила реакции') !== false) {
                    continue;
                }

                if (!isset($effectsMap[$key])) {
                    $effectsMap[$key] = 0;
                }

                $numericValue = (int) filter_var($parsed['value'], FILTER_SANITIZE_NUMBER_INT);

                $effectTypeId = $effect->effect_type_id ?? 1;
                $isPositive = $effectTypeId == 1 || $effectTypeId == 3 || $effectTypeId == 4;
                $isNegative = $effectTypeId == 2 || $effectTypeId == 6 || $effectTypeId == 7 || $effectTypeId == 8;

                if (str_starts_with($parsed['value'], '-')) {
                    $effectsMap[$key] -= $numericValue;
                } elseif (str_starts_with($parsed['value'], '+')) {
                    $effectsMap[$key] += $numericValue;
                } else {
                    if ($isPositive) {
                        $effectsMap[$key] += $numericValue;
                    } elseif ($isNegative) {
                        $effectsMap[$key] -= $numericValue;
                    } else {
                        $effectsMap[$key] += $numericValue;
                    }
                }
            }
        }

        // ========== ФОРМИРУЕМ ЭФФЕКТЫ ==========
        $effects = [];
        foreach ($effectsMap as $key => $value) {
            if ($value === 0) continue;
            $effects[] = [
                'key' => $key,
                'value' => $value,
                'display' => $value > 0 ? '+' . $value : (string) $value,
                'is_positive' => $value > 0,
                'is_negative' => $value < 0,
                'type' => $value > 0 ? 'positive' : 'negative',
            ];
        }

        usort($effects, function ($a, $b) {
            return strcmp($a['key'], $b['key']);
        });

        usort($actorReactions, function ($a, $b) {
            return strcmp($a['actor'], $b['actor']);
        });

        // ========== ОПРЕДЕЛЯЕМ МЕСЯЦ ==========
        $monthIndex = (($sceneOrder - 1) % 12) + 1;
        $monthName = $this->monthNames[$monthIndex] ?? 'Месяц ' . $monthIndex;

        return [
            'step' => 0,
            'month' => $monthName,
            'scene_order' => $sceneOrder,
            'scene_title' => $sceneTitle ?: 'Неизвестная сцена',
            'situation' => $situation,
            'has_delayed_effect' => $hasDelayedEffect,
            'delayed' => $delayedText,
            'date' => $date,
            'choice_description' => $choiceDescription ?: 'Нет данных о выборе',
            'effects' => $effects,
            'actor_reactions' => $actorReactions,
        ];
    }

    private function parseEffect($effect): array
    {
        $data = $effect->effect_data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            return ['key' => 'unknown', 'value' => '0', 'type' => 'unknown', 'is_positive' => false, 'is_negative' => false, 'display' => '0'];
        }

        $isPositive = str_starts_with($value, '+') || (is_numeric($value) && $value > 0);
        $isNegative = str_starts_with($value, '-') || (is_numeric($value) && $value < 0);

        return [
            'key' => $key,
            'value' => $value,
            'type' => $effect->effectType->name ?? 'unknown',
            'is_positive' => $isPositive,
            'is_negative' => $isNegative,
            'display' => $isPositive ? '+' . ltrim($value, '+') : $value,
        ];
    }

    private function extractActorName(string $eventName): string
    {
        $parts = explode(' - ', $eventName);
        if (count($parts) > 0) {
            $name = str_replace('актор', '', $parts[0]);
            $name = str_replace('Актор', '', $name);
            return trim($name);
        }

        // Если нет " - ", пробуем найти слово "актор" в названии
        if (strpos($eventName, 'актор') !== false || strpos($eventName, 'Актор') !== false) {
            $name = preg_replace('/\s*[—-]\s*.*$/', '', $eventName);
            $name = str_replace('актор', '', $name);
            $name = str_replace('Актор', '', $name);
            return trim($name);
        }

        return '';
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

    private function getInitialState(): array
    {
        $firstHistory = $this->game->gameHistories()->with('event')->first();
        if (!$firstHistory) {
            return [];
        }

        $choice = Choice::where('event_id', $firstHistory->event_id)->first();
        if (!$choice || !$choice->scene) {
            return [];
        }

        $preset = Preset::where('scenario_id', $choice->scene->scenario_id)
            ->where('difficulty', $this->game->difficulty)
            ->first();

        if (!$preset) {
            return [];
        }

        $settings = $preset->settings;
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        $state = [];
        if (is_array($settings)) {
            foreach ($settings as $item) {
                if (is_array($item) && isset($item['key']) && isset($item['value'])) {
                    $state[$item['key']] = (int) $item['value'];
                }
            }
        }

        return $state;
    }

    private function calculateDiff(): array
    {
        $result = [];

        foreach ($this->finalState as $key => $finalValue) {
            $initialValue = $this->initialState[$key] ?? 0;
            $diff = $finalValue - $initialValue;

            $result[$key] = [
                'initial' => $initialValue,
                'final' => $finalValue,
                'diff' => $diff,
                'diff_text' => $diff > 0 ? "+{$diff}" : ($diff < 0 ? "{$diff}" : "0"),
                'diff_color' => $diff > 0 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-gray-400'),
            ];
        }

        return $result;
    }

    private function buildStateFromHistory(): array
    {
        $firstHistory = $this->game->gameHistories()->first();
        if (!$firstHistory) {
            return [];
        }

        $choice = Choice::where('event_id', $firstHistory->event_id)->first();
        if (!$choice || !$choice->scene) {
            return [];
        }

        $preset = Preset::where('scenario_id', $choice->scene->scenario_id)
            ->where('difficulty', $this->game->difficulty)
            ->first();

        if (!$preset) {
            return [];
        }

        $settings = $preset->settings;
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        $state = [];
        if (is_array($settings)) {
            foreach ($settings as $item) {
                if (is_array($item) && isset($item['key']) && isset($item['value'])) {
                    $state[$item['key']] = (int) $item['value'];
                }
            }
        }

        $histories = $this->game->gameHistories()
            ->with('event.effects')
            ->orderBy('id')
            ->get();

        foreach ($histories as $history) {
            foreach ($history->event->effects as $effect) {
                $state = $this->applyEffectManual($state, $effect);
            }
        }

        return $state;
    }

    private function applyEffectManual(array $state, $effect): array
    {
        $data = $effect->effect_data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
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

    public function goToScenarios()
    {
        return redirect()->route('scenarios');
    }

    public function goToGames()
    {
        return redirect()->route('user.games');
    }
    /**
     * Переключение годовых вкладок (экраны 14–16)
     */
    public function setAnnualTab(string $tab): void
    {
        if (!in_array($tab, ['year-results', 'management-review', 'year-chronicle'], true)) {
            return;
        }

        $this->annualTab = $tab;
        $this->chronicleTargetId = null;
        $this->highlightedChronicleId = null;
    }

    /**
     * «Открыть в хронике ›» из разбора управления (экран 15 → 16)
     */
    public function openChronicleFromReview(string $entryId): void
    {
        $this->annualTab = 'year-chronicle';
        $this->chronicleFilter = 'all';
        $this->chronicleActorFilter = 'all';
        $this->chronicleTargetId = $entryId;
        $this->highlightedChronicleId = $entryId;
        if (!in_array($entryId, $this->expandedChronicleIds, true)) {
            $this->expandedChronicleIds[] = $entryId;
        }
        $this->scrollToChronicleEntry($entryId);
    }

    /**
     * Развернуть/свернуть запись хроники
     */
    public function toggleChronicleEntry(string $entryId): void
    {
        $index = array_search($entryId, $this->expandedChronicleIds, true);
        if ($index === false) {
            $this->expandedChronicleIds[] = $entryId;
        } else {
            unset($this->expandedChronicleIds[$index]);
            $this->expandedChronicleIds = array_values($this->expandedChronicleIds);
        }
        $this->chronicleTargetId = null;
        $this->highlightedChronicleId = null;
    }

    /**
     * Переход к связанному событию хроники
     */
    public function goToLinkedEntry(string $entryId): void
    {
        $this->chronicleFilter = 'all';
        $this->chronicleActorFilter = 'all';
        if (!in_array($entryId, $this->expandedChronicleIds, true)) {
            $this->expandedChronicleIds[] = $entryId;
        }
        $this->chronicleTargetId = $entryId;
        $this->highlightedChronicleId = $entryId;
        $this->scrollToChronicleEntry($entryId);
    }

    public function resetChronicleFilters(): void
    {
        $this->chronicleFilter = 'all';
        $this->chronicleActorFilter = 'all';
        $this->chronicleSort = 'chronological';
        $this->chronicleTargetId = null;
        $this->highlightedChronicleId = null;
    }

    /**
     * Прокрутка к записи хроники после морфинга Livewire
     */
    private function scrollToChronicleEntry(string $entryId): void
    {
        $this->js(<<<JS
            setTimeout(() => {
                const body = document.querySelector('.document-sheet--year-chronicle .chronicle-document__body');
                const target = body ? body.querySelector('[data-chronicle-entry-id="{$entryId}"]') : null;
                if (body && target) {
                    const top = target.getBoundingClientRect().top - body.getBoundingClientRect().top + body.scrollTop - 12;
                    body.scrollTo({ top: top, behavior: 'smooth' });
                }
            }, 60);
        JS);
    }

    /**
     * Подтверждение переигровки: возврат к выбору сценария (текущая игра уже завершена)
     */
    public function openReplayConfirmation(): void
    {
        $this->replayConfirmationOpen = true;
    }

    public function closeReplayConfirmation(): void
    {
        $this->replayConfirmationOpen = false;
    }

    public function confirmReplay(): void
    {
        $this->replayConfirmationOpen = false;

        redirect()->route('scenarios');
    }

    /**
     * Данные годовых экранов (источник — PrototypeContent)
     */
    /**
    /**
     * Данные годовых экранов: статичный каркас + реальные итоги из parametersWithDiff
     */
    public function getAnnualResults(): array
    {
        $static = PrototypeContent::annualResults();

        // Только валидные показатели (отбрасываем служебные/тестовые ключи, напр. «Бла бла бла»)
        $validKeys = ParameterDefinition::pluck('name')->toArray();

        $strengthened = [];
        $weakened = [];
        $vulnerable = [];

        foreach ($this->parametersWithDiff as $key => $data) {
            if (!in_array($key, $validKeys, true)) {
                continue;
            }

            $diff  = (int) $data['diff'];
            $final = (int) $data['final'];
            $goodWhenUp = $this->isPositiveChangeGood($key);

            $improved = $goodWhenUp ? $diff > 0 : $diff < 0;
            $worsened = $goodWhenUp ? $diff < 0 : $diff > 0;

            if ($improved) {
                $strengthened[] = $key;
            }
            if ($worsened) {
                $weakened[] = $key;
            }
            if ($final < 50) {
                $vulnerable[] = $key;
            }
        }

        return array_merge($static, [
            'scenesCompleted' => $this->totalSteps,
            'strengthened' => $strengthened ?: ['Существенных улучшений не зафиксировано'],
            'weakened' => $weakened ?: ['Существенных ухудшений не зафиксировано'],
            'vulnerable' => $vulnerable ?: ['Критически слабых зон не отмечено'],
        ]);
    }

    /**
     * Для каких показателей рост — это хорошо.
     * Для «Конфликтная напряженность» и «Риск управленческого сбоя» рост — это плохо.
     */
    private function isPositiveChangeGood(string $key): bool
    {
        return !in_array($key, ['Конфликтная напряженность', 'Риск управленческого сбоя'], true);
    }

    public function getManagementReview(): array
    {
        $steps = $this->historyData;
        $totalDecisions = count($steps);

        $totalActorReactions = collect($steps)->sum(fn ($s) => count($s['actor_reactions']));
        $positive = collect($steps)->flatMap(fn ($s) => $s['effects'])->filter(fn ($e) => $e['is_positive'])->count();
        $negative = collect($steps)->flatMap(fn ($s) => $s['effects'])->filter(fn ($e) => $e['is_negative'])->count();

        $improved = collect($this->parametersWithDiff)->filter(fn ($d) => $d['diff'] > 0);
        $worsened = collect($this->parametersWithDiff)->filter(fn ($d) => $d['diff'] < 0);

        // Сопоставление месяцев с хроникой, чтобы «Открыть в хронике» вело к реальной записи
        $chronicleByMonth = collect($this->getChronicle()['entries'] ?? [])->keyBy('month');

        // Поворотные решения — шаги с наибольшим числом реакций акторов
        $turningPoints = collect($steps)
            ->map(fn ($s, $i) => ['i' => $i, 'r' => count($s['actor_reactions']), 's' => $s])
            ->filter(fn ($t) => $t['r'] > 0)
            ->sortByDesc('r')
            ->take(4)
            ->map(function ($t) use ($chronicleByMonth) {
                $s = $t['s'];
                $reaction = $s['actor_reactions'][0] ?? null;
                $effect = $s['effects'][0] ?? null;
                return [
                    'id' => $chronicleByMonth[$s['month']]['id'] ?? ('step-' . ($t['i'] + 1)),
                    'month' => $s['month'],
                    'situation' => $s['scene_title'],
                    'decision' => $s['choice_description'],
                    'result' => $effect ? $effect['key'] . ' ' . $effect['display'] : 'Решение применено',
                    'consequence' => $reaction ? $reaction['actor'] . ' — ' . $reaction['event'] : 'Реакция аппарата',
                ];
            })->values()->toArray();

        $strengths = $improved->map(fn ($d, $key) => [
            'title' => 'Укрепление: ' . $key,
            'description' => 'Изменение за год: ' . $d['diff_text'] . '. Положительная динамика.',
        ])->values()->toArray();

        $risks = $worsened->map(fn ($d, $key) => [
            'title' => 'Риск: ' . $key,
            'description' => 'Изменение за год: ' . $d['diff_text'] . '. Требует внимания.',
        ])->values()->toArray();

        if (empty($strengths)) {
            $strengths = [['title' => 'Удержание стабильности', 'description' => 'Часть параметров удержана без ухудшения.']];
        }
        if (empty($risks)) {
            $risks = [['title' => 'Отсутствие выраженных ухудшений', 'description' => 'Значимых ухудшений за год не зафиксировано.']];
        }

        $profileTitle = $totalActorReactions > $totalDecisions
            ? 'Оперативный руководитель с высокой вовлечённостью'
            : 'Оперативный руководитель';

        $profileNarrative = [
            "За год принято решений: {$totalDecisions}. Суммарных реакций акторов: {$totalActorReactions}.",
            "Положительных изменений параметров: {$positive}, отрицательных: {$negative}.",
            $improved->count() > 0 ? 'Укреплены: ' . $improved->keys()->implode(', ') . '.' : 'Выраженных укреплений не зафиксировано.',
            $worsened->count() > 0 ? 'Ослабли: ' . $worsened->keys()->implode(', ') . '.' : 'Значимых ухудшений не зафиксировано.',
        ];

        $markers = [
            'Решений: ' . $totalDecisions,
            'Реакций акторов: ' . $totalActorReactions,
            'Улучшено параметров: ' . $improved->count(),
        ];

        $keyPattern = [
            "Положительных изменений: {$positive}, отрицательных: {$negative}.",
            $worsened->count() > $improved->count()
                ? 'Динамика указывает на накопление системных рисков.'
                : 'Динамика в целом устойчивая, отдельные параметры требуют внимания.',
        ];

        $recommendations = $worsened->map(fn ($d, $key) => [
            'title' => 'Восстановить: ' . $key,
            'description' => 'Вернуть параметр к устойчивому уровню через процедурные решения.',
        ])->values()->toArray();
        if (empty($recommendations)) {
            $recommendations = [['title' => 'Закрепить процедуры', 'description' => 'Зафиксировать успешные практики в регулярных процедурах.']];
        }

        $priorities = $worsened->keys()->take(3)->values()->toArray();
        if (empty($priorities)) {
            $priorities = ['Устойчивые процедуры'];
        }

        $prioritySummary = $worsened->count() > 0
            ? 'Главная задача — восстановить ослабшие параметры: ' . $worsened->keys()->implode(', ') . '.'
            : 'Главная задача — закрепить устойчивые процедуры.';

        return [
            'context' => 'Сценарий «' . $this->scenarioName . '» · Глава округа · ' . $totalDecisions . ' сцен',
            'profileTitle' => $profileTitle,
            'profileNarrative' => $profileNarrative,
            'markers' => $markers,
            'strengths' => $strengths,
            'risks' => $risks,
            'turningPoints' => $turningPoints,
            'keyPattern' => $keyPattern,
            'recommendations' => $recommendations,
            'priorities' => $priorities,
            'prioritySummary' => $prioritySummary,
            'sidebar' => [
                'profile' => ['title' => $profileTitle, 'items' => $markers],
                'strength' => $strengths[0],
                'risk' => $risks[0],
                'recommendation' => $recommendations[0],
            ],
        ];
    }

    public function getChronicle(): array
    {
        $entries = [];
        foreach ($this->historyData as $index => $step) {
            $actors = collect($step['actor_reactions'])->pluck('actor')->unique()->values()->toArray();
            $effectsSummary = collect($step['effects'])
                ->map(fn ($e) => $e['key'] . ' ' . $e['display'])
                ->implode(', ');

            $entries[] = [
                'id' => 'step-' . ($index + 1),
                'month' => $step['month'],
                'scene' => $step['scene_order'],
                'title' => $step['scene_title'],
                'situation' => '',
                'decision' => $step['choice_description'],
                'result' => $effectsSummary ?: 'Решение применено.',
                'delayed' => '',
                'related' => '',
                'relatedId' => null,
                'recommendation' => '',
                'actors' => $actors,
                'turningPoint' => count($step['actor_reactions']) > 0,
                'hasDelayedEffect' => false,
            ];
        }

        return [
            'finalState' => $this->statistics['scenario_name'] ?? '',
            'entries' => $entries,
        ];
    }

    /**
     * Видимые записи хроники с учётом фильтров и порядка
     */
    public function getVisibleChronicleEntries(): array
    {
        $entries = collect($this->getChronicle()['entries'] ?? []);

        $entries = $entries->filter(function ($entry) {
            if ($this->chronicleFilter === 'turning' && empty($entry['turningPoint'])) {
                return false;
            }
            if ($this->chronicleFilter === 'delayed' && empty($entry['hasDelayedEffect'])) {
                return false;
            }
            return $this->chronicleActorFilter === 'all' || in_array($this->chronicleActorFilter, $entry['actors'] ?? [], true);
        });

        if ($this->chronicleSort === 'reverse') {
            $entries = $entries->reverse();
        }

        return $entries->values()->toArray();
    }

    /**
     * Сводка хроники (экран 16)
     */
    public function getChronicleSummary(): array
    {
        $entries = collect($this->getChronicle()['entries'] ?? []);

        return [
            'scenes' => $entries->count(),
            'decisions' => $entries->count(),
            'turning' => $entries->filter(fn ($e) => !empty($e['turningPoint']))->count(),
            'delayed' => $entries->filter(fn ($e) => !empty($e['hasDelayedEffect']))->count(),
            'shown' => count($this->getVisibleChronicleEntries()),
            'finalState' => $this->getChronicle()['finalState'] ?? '',
        ];
    }

    public function isChronicleExpanded(string $entryId): bool
    {
        return in_array($entryId, $this->expandedChronicleIds, true);
    }

    public function render()
    {
        return view('livewire.game-results')
            ->layout('layouts.game')
            ->title('Результаты игры');
    }
}
