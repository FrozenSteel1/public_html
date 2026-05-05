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
}
