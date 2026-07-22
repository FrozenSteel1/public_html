<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class DelayedMessageHandler implements EffectHandlerInterface
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

        $message = $data['message'] ?? $data['text'] ?? null;
        $delay = $data['delay'] ?? 0;
        $type = $data['type'] ?? 'info';

        if ($message) {
            $delayedMessages = session()->get('delayed_game_messages', []);

            $exists = false;
            foreach ($delayedMessages as $dm) {
                if ($dm['message'] === $message) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $delayedMessages[] = [
                    'message' => $message,
                    'type' => $type,
                    'delay' => $delay,
                    'current_delay' => 0,
                    'created_at' => now()->toDateTimeString(),
                ];
                session()->put('delayed_game_messages', $delayedMessages);
            }

            Log::info('Delayed message effect applied', [
                'message' => $message,
                'delay' => $delay,
                'type' => $type,
            ]);
        }

        return $currentState;
    }

    public function getType(): string
    {
        return 'Отложенное сообщение';
    }
}
