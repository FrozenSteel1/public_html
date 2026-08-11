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
                // Опциональные метаданные письма (UI V1, экран 10).
                // Если ключей нет в effect_data — вью использует безопасные фолбэки.
                'actor' => $data['actor'] ?? null,
                'sender' => $data['sender'] ?? null,
                'subject' => $data['subject'] ?? null,
                'priority' => $data['priority'] ?? null,
                'urgent' => $data['urgent'] ?? null,
                'time' => now()->format('d.m.Y H:i'),
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
