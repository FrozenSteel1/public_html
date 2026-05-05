<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameHistory extends Model
{
    protected $fillable = [
        'game_id',
        'event_id',
    ];

    // Связь с таблицей games (принадлежит игре)
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    // Связь с таблицей events (принадлежит событию)
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
