<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choice extends Model
{
    protected $fillable = [
        'scene_id',
        'description',
        'event_id',
        'choice_type_id',
        'conditions',
        'order',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    // Связь с таблицей scenes (принадлежит сцене)
    public function scene(): BelongsTo
    {
        return $this->belongsTo(Scene::class);
    }

    // Связь с таблицей events (принадлежит событию)
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Связь с таблицей choice_types (тип ответа игрока)
    public function choiceType(): BelongsTo
    {
        return $this->belongsTo(ChoiceType::class);
    }
}
