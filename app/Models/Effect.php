<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
    protected $fillable = [
        'event_id',
        'effect_type_id',
        'effect_data',
    ];

    protected $casts = [
        'effect_data' => 'array',
    ];

    // Связь с таблицей events (принадлежит событию)
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Связь с таблицей effect_types (принадлежит типу эффекта)
    public function effectType(): BelongsTo
    {
        return $this->belongsTo(EffectType::class);
    }
}
