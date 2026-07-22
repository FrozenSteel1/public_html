<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;

interface EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array;
    public function getType(): string;
}
