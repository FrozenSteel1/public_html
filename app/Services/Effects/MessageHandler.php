<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class MessageHandler implements EffectHandlerInterface
{
    /** Контекст применения эффекта (кто породил событие) */
    private array $context = [];

    public function setContext(array $context): void
    {
        $this->context = $context;
    }
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

        // Кто породил событие — из контекста применения эффектов (GameService):
        // source=actor → имя актора, чей триггер сработал; иначе — ход игрока
        $eventName = trim($effect->event->name ?? '');
        $sender = ($this->context['source'] ?? null) === 'actor'
            ? ($this->context['actor_name'] ?? 'Актор')
            : 'Результат действий игрока';

        Log::info('📥 [MessageHandler] Старт обработки', [
            'effect_id' => $effect->id,
            'parsed_message' => $message,
            'sender' => $sender,
        ]);

        if ($message) {
            $messages = session()->get('game_messages', []);
            $messages[] = [
                'text' => $message,
                'type' => $type,
                'timestamp' => now()->toDateTimeString(),
                'actor' => $sender,
                'sender' => $sender,
                'subject' => $eventName !== '' ? $eventName : 'Сообщение',
            ];
            session()->put('game_messages', $messages);

            Log::info('✅ [MessageHandler] Успешно добавлено в сессию', [
                'message_text' => $message,
                'sender' => $sender,
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
