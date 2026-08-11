<?php
namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ParameterChangeHandler implements EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        // $data УЖЕ массив благодаря Accessor в модели Effect
        $data = $effect->effect_data;

        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;

        if (!$key || !$value) {
            Log::warning('ParameterChangeHandler: пропущен эффект', ['effect_id' => $effect->id]);
            return $currentState;
        }

        $numericValue = abs((int) filter_var($value, FILTER_SANITIZE_NUMBER_INT));

        if (!isset($currentState[$key])) {
            $currentState[$key] = 0;
        }

        $currentState[$key] += $numericValue;
        $currentState[$key] = max(0, min(100, $currentState[$key]));

        return $currentState;
    }

    public function getType(): string
    {
        return 'Повышение показателя';
    }
}
