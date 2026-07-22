<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;

class ParameterDecreaseHandler implements EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        // Тот же код, что и в ParameterChangeHandler, но для type 2
        $data = json_decode($effect->effect_data, true);
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            return $currentState;
        }

        $numericValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        $operation = str_starts_with($value, '+') ? '+' :
            (str_starts_with($value, '-') ? '-' : '=');

        if (!isset($currentState[$key])) {
            $currentState[$key] = 0;
        }

        switch ($operation) {
            case '+':
                $currentState[$key] += $numericValue;
                break;
            case '-':
                $currentState[$key] -= $numericValue;
                break;
            case '=':
                $currentState[$key] = $numericValue;
                break;
        }

        $currentState[$key] = max(0, min(100, $currentState[$key]));

        return $currentState;
    }

    public function getType(): string
    {
        return 'Снижение показателя';
    }
}
