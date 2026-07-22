<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class EffectManager
{
    private array $handlers = [];

    public function __construct()
    {
        $this->registerHandlers();
    }

    /**
     * Зарегистрировать все обработчики эффектов
     */
    private function registerHandlers(): void
    {
        $handlers = [
            new ParameterChangeHandler(),
            new ParameterDecreaseHandler(),
            new SupportHandler(),
            new SupportHandler(), // Для type 4 (Содействовать) используем тот же обработчик
            new MessageHandler(), // Для type 5 (Подать сигнал риска) - используем как сообщение
            new MessageHandler(), // Для type 6 (Критиковать) - используем как сообщение
            new MessageHandler(), // Для type 7 (Тормозить) - используем как сообщение
            new MessageHandler(), // Для type 8 (Блокировать) - используем как сообщение
            new MessageHandler(), // Для type 9 (Отойти в сторону) - используем как сообщение
            new ParameterChangeHandler(), // Для type 10 (Сила реакции) - применяем как изменение параметров
            new SceneTransitionHandler(), // Для type 11 (Смена сцены)
            new MessageHandler(), // Для type 12 (Сообщение) - добавим позже
            new DelayedMessageHandler(), // Для type 13 (Отложенное сообщение) - добавим позже
        ];

        foreach ($handlers as $handler) {
            $this->handlers[$handler->getType()] = $handler;
        }
    }

    /**
     * Обработать эффект
     */
    public function handle(Game $game, Effect $effect, array $currentState): array
    {
        $effectTypeName = $effect->effectType->name ?? 'Неизвестный тип';

        $handler = $this->handlers[$effectTypeName] ?? null;

        if ($handler) {
            try {
                Log::info('Processing effect', [
                    'type' => $effectTypeName,
                    'effect_id' => $effect->id,
                ]);
                return $handler->handle($game, $effect, $currentState);
            } catch (\Exception $e) {
                Log::error('Error processing effect', [
                    'type' => $effectTypeName,
                    'error' => $e->getMessage(),
                ]);
                return $currentState;
            }
        }

        Log::warning('No handler found for effect type', [
            'type' => $effectTypeName,
        ]);

        return $currentState;
    }

    /**
     * Получить все отложенные сообщения и очистить их
     */
    public function getDelayedMessages(): array
    {
        $messages = session()->get('delayed_game_messages', []);
        $ready = [];
        $remaining = [];

        foreach ($messages as $msg) {
            if ($msg['current_delay'] >= $msg['delay']) {
                $ready[] = $msg;
            } else {
                $msg['current_delay']++;
                $remaining[] = $msg;
            }
        }

        session()->put('delayed_game_messages', $remaining);

        return $ready;
    }

    /**
     * Получить все сообщения и очистить их
     */
    public function getMessages(): array
    {
        $messages = session()->get('game_messages', []);
        Log::info('EffectManager::getMessages вызван', [
            'messages_count' => count($messages),
            'messages' => $messages,
        ]);
        session()->put('game_messages', []);
        return $messages;
    }

    /**
     * Получить поддержку акторов
     */
    public function getSupports(): array
    {
        $supports = session()->get('actor_supports', []);
        session()->put('actor_supports', []);
        return $supports;
    }

    /**
     * Проверить, есть ли принудительный переход на сцену
     */
    public function getForcedScene(): ?array
    {
        $forced = session()->get('forced_next_scene');
        if ($forced) {
            session()->forget('forced_next_scene');
        }
        return $forced;
    }
}
