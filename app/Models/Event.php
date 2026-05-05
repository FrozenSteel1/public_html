<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Связь с таблицей effects (одно событие имеет много эффектов)
    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

    // Связь с таблицей choices (одно событие может использоваться во многих выборах)
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }

    // Связь с таблицей game_histories (одно событие может быть во многих историях игр)
    public function gameHistories(): HasMany
    {
        return $this->hasMany(GameHistory::class);
    }
}
