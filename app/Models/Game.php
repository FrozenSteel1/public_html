<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'status',
    ];

    // Связь с таблицей users (принадлежит пользователю)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с таблицей companies (принадлежит компании)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Связь с таблицей game_histories (одна игра имеет много записей истории)
    public function gameHistories(): HasMany
    {
        return $this->hasMany(GameHistory::class);
    }
}
