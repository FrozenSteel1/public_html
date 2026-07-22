<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ParameterChangeHandler implements EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        $data = json_decode($effect->effect_data, true);
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            Log::warning('ParameterChangeHandler: пропущен эффект', [
                'effect_id' => $effect->id,
                'data' => $data,
            ]);
            return $currentState;
        }

        // Берём модуль числа, чтобы всегда прибавлять положительное значение
        $numericValue = abs((int) filter_var($value, FILTER_SANITIZE_NUMBER_INT));

        Log::info('ParameterChangeHandler: увеличение', [
            'key' => $key,
            'value' => $numericValue,
            'old_state' => $currentState[$key] ?? 0,
        ]);

        if (!isset($currentState[$key])) {
            $currentState[$key] = 0;
        }

        $currentState[$key] += $numericValue;
        $currentState[$key] = max(0, min(100, $currentState[$key]));

        Log::info('ParameterChangeHandler: результат', [
            'key' => $key,
            'new_state' => $currentState[$key],
        ]);

        return $currentState;
    }

    public function getType(): string
    {
        return 'Повышение показателя';
    }
}
