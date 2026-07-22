<?php

namespace App\Services\Effects;

use App\Models\Effect;
use App\Models\Game;
use App\Models\Scene;
use Illuminate\Support\Facades\Log;

class SceneTransitionHandler implements EffectHandlerInterface
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

        $targetSceneId = $data['target_scene_id'] ?? null;
        $mode = $data['mode'] ?? 'replace';

        if ($targetSceneId) {
            $targetScene = Scene::find($targetSceneId);
            if ($targetScene) {
                session()->put('forced_next_scene', [
                    'scene_id' => $targetSceneId,
                    'mode' => $mode,
                ]);

                Log::info('Scene transition effect applied', [
                    'target_scene_id' => $targetSceneId,
                    'mode' => $mode,
                ]);
            }
        }

        return $currentState;
    }

    public function getType(): string
    {
        return 'Смена сцены';
    }
}
