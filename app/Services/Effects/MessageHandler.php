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
        if (is_string($data)) { $data = json_decode($data, true); }
        if (is_string($data)) { $data = json_decode($data, true); }

        $message = $data['message'] ?? $data['text'] ?? null;
        $type = $data['type'] ?? 'info';

        // 📥 ЛОГИРОВАНИЕ ПАРСИНГА
        Log::info('📥 [MessageHandler] Старт обработки', [
            'effect_id' => $effect->id,
            'parsed_message' => $message,
        ]);

        if ($message) {
            $messages = session()->get('game_messages', []);
            $messages[] = [
                'text' => $message,
                'type' => $type,
                'timestamp' => now()->toDateTimeString(),
            ];
            session()->put('game_messages', $messages);

            // ✅ ЛОГИРОВАНИЕ УСПЕШНОЙ ЗАПИСИ В СЕССИЮ
            Log::info('✅ [MessageHandler] Успешно добавлено в сессию', [
                'message_text' => $message,
                'total_in_session' => count(session()->get('game_messages', [])),
                'session_id' => session()->getId(),
            ]);
        } else {
            Log::warning('❌ [MessageHandler] Пустое сообщение или не найдено в data', [
                'data' => $data,
            ]);
        }

        return $currentState;
    }

    public function getType(): string
    {
        return 'Сообщение';
    }
}
