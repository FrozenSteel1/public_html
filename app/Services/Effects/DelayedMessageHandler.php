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
                    // ===== ядро (без изменений) =====
                    'message' => $message,
                    'type' => $type,
                    'delay' => $delay,
                    'current_delay' => 0,
                    'created_at' => now()->toDateTimeString(),
                    // ===== опциональные метаданные источника (UI V1, экран 13) =====
                    // Если ключей нет в effect_data — вью использует безопасные фолбэки.
                    'actor' => $data['actor'] ?? null,
                    'source_month' => $data['source_month'] ?? null,
                    'source_scene' => $data['source_scene'] ?? null,
                    'source_decision' => $data['source_decision'] ?? null,
                    'delay_label' => $data['delay_label'] ?? null,
                    'title' => $data['title'] ?? null,
                    'subtitle' => $data['subtitle'] ?? null,
                    'status' => $data['status'] ?? null,
                    'introduction' => $data['introduction'] ?? null,
                    'consequences' => $data['consequences'] ?? null,
                    'conclusion' => $data['conclusion'] ?? null,
                    'impact' => $data['impact'] ?? $data['currentImpact'] ?? null,
                    'signals' => $data['signals'] ?? null,
                ];
                session()->put('delayed_game_messages', $delayedMessages);
            }

            Log::info('DelayedMessageHandler: создание', [
                'message' => $message,
                'delay' => $delay,
                'type' => $type,
                'source_month' => $data['source_month'] ?? null,
                'source_scene' => $data['source_scene'] ?? null,
                'source_decision' => $data['source_decision'] ?? null,
                'session_before' => session()->get('delayed_game_messages', []),
            ]);
        }

        return $currentState;
    }

    public function getType(): string
    {
        return 'Отложенное сообщение';
    }
}
