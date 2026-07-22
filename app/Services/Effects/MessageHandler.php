<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class MessageHandler implements EffectHandlerInterface
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
        $type = $data['type'] ?? 'info';

        if ($message) {
            $messages = session()->get('game_messages', []);
            $messages[] = [
                'text' => $message,
                'type' => $type,
                'timestamp' => now()->toDateTimeString(),
            ];
            session()->put('game_messages', $messages);

            Log::info('Message effect applied', [
                'message' => $message,
                'type' => $type,
                'session_messages' => session()->get('game_messages'),
            ]);
        }

        return $currentState;
    }

    public function getType(): string
    {
        return 'Сообщение';
    }
}
