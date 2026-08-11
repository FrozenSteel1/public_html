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

        // Дефект данных (id 178/195/215): хвостовые пробелы в ключах/значениях JSON
        if (is_array($data)) {
            $normalized = [];
            foreach ($data as $key => $value) {
                $normalized[trim((string) $key)] = is_string($value) ? trim($value) : $value;
            }
            $data = $normalized;
        }

        $message = $data['message'] ?? $data['text'] ?? null;
        $type = $data['type'] ?? 'info';

        // Отправитель/источник для модалки (экран 10) — из имени события эффекта
        $eventName = $effect->event->name ?? '';
        $actorName = $eventName !== '' ? trim(explode(' - ', $eventName)[0]) : '';

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
                'actor' => $actorName !== '' ? $actorName : 'Актор',
                'sender' => $eventName !== '' ? $eventName : 'Служебное сообщение',
            ];
            session()->put('game_messages', $messages);

            Log::info('✅ [MessageHandler] Успешно добавлено в сессию', [
                'message_text' => $message,
                'total_in_session' => count(session()->get('game_messages', [])),
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
