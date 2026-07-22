<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;

interface EffectHandlerInterface
{
    /**
     * Обработать эффект
     */
    public function handle(Game $game, Effect $effect, array $currentState): array;

    /**
     * Получить тип эффекта, который обрабатывает этот хендлер
     */
    public function getType(): string;
}
