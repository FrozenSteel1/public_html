<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scene extends Model
{
    protected $fillable = [
        'scenario_id',
        'order',
        'title',
        'situation',
        'additional_data',
    ];

    protected $casts = [
        'additional_data' => 'array',
    ];

    // Связь с таблицей scenarios (принадлежит сценарию)
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    // Связь с таблицей choices (одна сцена имеет много выборов)
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class)->orderBy('order');
    }
    /**
     * Получить акторов, участвующих в сцене
     * (из additional_data или через связи)
     */
    public function getActors(): array
    {
        // Если в additional_data есть список акторов
        $data = $this->additional_data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        // Проверяем, есть ли в additional_data поле 'actors'
        if (is_array($data) && isset($data['actors'])) {
            return $data['actors'];
        }

        // Если нет - возвращаем всех акторов или пустой массив
        return [];
    }
}
