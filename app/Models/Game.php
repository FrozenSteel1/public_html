<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;



class Game extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'current_scene_id',
        'difficulty',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Связь с таблицей users (принадлежит пользователю)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с таблицей companies (принадлежит компании)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Связь с таблицей game_histories (одна игра имеет много записей истории)
    public function gameHistories(): HasMany
    {
        return $this->hasMany(GameHistory::class);
    }

    /**
     * Связь с текущей сценой
     */
    public function currentScene(): BelongsTo
    {
        return $this->belongsTo(Scene::class, 'current_scene_id');
    }



    /**
     * Применить эффект к состоянию
     */
    private function applyEffect(array $state, Effect $effect): array
    {
        $data = json_decode($effect->effect_data, true);
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            return $state;
        }

        // Парсим значение (например, "+5", "-3", "10")
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

        // Ограничиваем значения диапазоном 0-100
        $state[$key] = max(0, min(100, $state[$key]));

        return $state;
    }

    /**
     * Проверить, завершена ли игра
     */
    public function isFinished(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    /**
     * Получить следующую сцену по порядку
     */
    public function getNextScene(): ?Scene
    {
        if (!$this->current_scene_id) {
            return null;
        }

        $currentOrder = $this->currentScene->order;
        $scenarioId = $this->currentScene->scenario_id;

        return Scene::where('scenario_id', $scenarioId)
            ->where('order', '>', $currentOrder)
            ->orderBy('order')
            ->first();
    }




    /**
     * Применить эффекты события к состоянию
     */
    public function applyEventEffects(array $state, Event $event): array
    {
        foreach ($event->effects as $effect) {
            $state = $this->applyEffect($state, $effect);
        }
        return $state;
    }

    /**
     * Получить следующую сцену на основе эффектов события
     * Если есть эффект типа "Смена сцены" - возвращаем целевую сцену
     * Иначе - следующую по порядку
     */
    public function getNextSceneAfterEvent(Event $event): ?Scene
    {
        // Ищем эффект типа "Смена сцены"
        $sceneTransitionEffect = $event->effects->first(function ($effect) {
            return $effect->effectType->name === 'Смена сцены';
        });

        if ($sceneTransitionEffect) {
            $data = $sceneTransitionEffect->effect_data;
            $targetSceneId = $data['target_scene_id'] ?? null;

            if ($targetSceneId) {
                return Scene::find($targetSceneId);
            }
        }

        // Если нет эффекта смены сцены - берем следующую по порядку
        return $this->getNextScene();
    }
    /**
     * Получить текущее состояние игры с кэшированием
     */
    public function getCurrentState(bool $forceRefresh = false): array
    {
        $cacheKey = "game_state_{$this->id}";

        if (!$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $state = $this->buildCurrentState();
        Cache::put($cacheKey, $state, 300); // 5 минут

        return $state;
    }

    /**
     * Построить состояние из истории
     */
    private function buildCurrentState(): array
    {
        $preset = Preset::where('scenario_id', $this->currentScene->scenario_id)
            ->where('difficulty', $this->difficulty)
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

        $state = is_array($settings) ? $settings : [];

        $histories = $this->gameHistories()
            ->with('event.effects')
            ->orderBy('id')
            ->get();

        foreach ($histories as $history) {
            foreach ($history->event->effects as $effect) {
                $state = $this->applyEffect($state, $effect);
            }
        }

        return $state;
    }
}
