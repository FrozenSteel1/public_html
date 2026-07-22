<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ParameterDecreaseHandler implements EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        $data = json_decode($effect->effect_data, true);
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            Log::warning('ParameterDecreaseHandler: пропущен эффект', [
                'effect_id' => $effect->id,
                'data' => $data,
            ]);
            return $currentState;
        }

        $numericValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        // ========== ГЛАВНОЕ ИЗМЕНЕНИЕ ==========
        // Берём модуль числа, чтобы всегда вычитать положительное значение
        $numericValue = abs($numericValue);
        // =======================================

        Log::info('ParameterDecreaseHandler: уменьшение', [
            'key' => $key,
            'value' => $numericValue,
            'old_state' => $currentState[$key] ?? 0,
        ]);

        if (!isset($currentState[$key])) {
            $currentState[$key] = 0;
        }

        $currentState[$key] -= $numericValue;
        $currentState[$key] = max(0, min(100, $currentState[$key]));

        Log::info('ParameterDecreaseHandler: результат', [
            'key' => $key,
            'new_state' => $currentState[$key],
        ]);

        return $currentState;
    }

    public function getType(): string
    {
        return 'Снижение показателя';
    }
}
