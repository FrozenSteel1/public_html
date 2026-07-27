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

    public function render()
    {
        return view('livewire.game-results')
            ->layout('layouts.app')
            ->title('Результаты игры');
    }
}
