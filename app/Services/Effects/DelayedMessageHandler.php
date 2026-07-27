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

        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $message = $data['message'] ?? $data['text'] ?? null;
        $delay = (int) ($data['delay'] ?? 0);
        $type = $data['type'] ?? 'info';

        if ($message && $delay > 0) {
            $delayedMessages = session()->get('delayed_game_messages', []);

            // Проверяем, нет ли уже такого сообщения
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
                    'current_delay' => 0,  // <-- ВАЖНО: начинаем с 0
                    'created_at' => now()->toDateTimeString(),
                ];
                session()->put('delayed_game_messages', $delayedMessages);
            }

            Log::info('Delayed message created', [
                'message' => $message,
                'delay' => $delay,
                'type' => $type,
            ]);
        }

        Log::info('DelayedMessageHandler: создание', [
            'message' => $message,
            'delay' => $delay,
            'type' => $type,
            'session_before' => session()->get('delayed_game_messages', []),
        ]);
        return $currentState;
    }

    public function getType(): string
    {
        return 'Отложенное сообщение';
    }
}
