<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class SupportHandler implements EffectHandlerInterface
{
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        $data = $effect->effect_data;

        // Если data - строка JSON, декодируем
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        // Если data всё ещё строка (двойное экранирование)
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $actorId = $data['actor_id'] ?? null;
        $message = $data['message'] ?? $data['text'] ?? null;
        $modifier = $data['modifier'] ?? null;
        $targetKey = $data['target_key'] ?? null;

        $supports = session()->get('actor_supports', []);
        $supports[] = [
            'actor_id' => $actorId,
            'message' => $message,
            'modifier' => $modifier,
            'target_key' => $targetKey,
            'timestamp' => now()->toDateTimeString(),
        ];
        session()->put('actor_supports', $supports);

        if ($targetKey && $modifier) {
            if (!isset($currentState[$targetKey])) {
                $currentState[$targetKey] = 0;
            }
            $currentState[$targetKey] += $modifier;
            $currentState[$targetKey] = max(0, min(100, $currentState[$targetKey]));
        }

        Log::info('Support effect applied', [
            'actor_id' => $actorId,
            'message' => $message,
        ]);

        return $currentState;
    }

    public function getType(): string
    {
        return 'Поддержать';
    }
}
