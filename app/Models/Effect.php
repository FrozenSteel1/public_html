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

    // УБИРАЕМ $casts, чтобы избежать конфликта с Accessor
    // protected $casts = ['effect_data' => 'array'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function effectType(): BelongsTo
    {
        return $this->belongsTo(EffectType::class);
    }

    protected function effectData(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $data = $value;

                // Первое декодирование
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = $decoded;
                    }
                }

                // Второе декодирование (если было двойное экранирование в БД)
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = $decoded;
                    }
                }

                // Нормализация: если это массив, убираем случайные пробелы в ключах
                if (is_array($data)) {
                    $normalized = [];
                    foreach ($data as $k => $v) {
                        $normalized[trim($k)] = $v;
                    }
                    return $normalized;
                }

                return $data;
            }
        );
    }
}
