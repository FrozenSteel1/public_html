<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;


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
    protected function effectData(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Если JSON - декодируем
                if (is_string($value)) {
                    $decoded = json_decode($value, true);

                    // Если декодировалось и это строка с JSON внутри
                    if (is_string($decoded) && str_starts_with($decoded, '{')) {
                        $decoded = json_decode($decoded, true);
                    }

                    return $decoded ?? $value;
                }
                return $value;
            }
        );
    }
}
