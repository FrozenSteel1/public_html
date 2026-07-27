<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'event_id',
        'scene_id',  // <-- Добавляем
        'source',
    ];

    protected $casts = [
        'source' => 'string',
    ];

    // ========== КОНСТАНТЫ ==========
    public const SOURCE_PLAYER = 'player';
    public const SOURCE_ACTOR = 'actor';
    public const SOURCE_SYSTEM = 'system';

    // ========== ОТНОШЕНИЯ ==========

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scene(): BelongsTo  // <-- Добавляем связь со сценой
    {
        return $this->belongsTo(Scene::class);
    }

    // ========== МЕТОДЫ ==========

    public function isPlayerMove(): bool
    {
        return $this->source === self::SOURCE_PLAYER;
    }

    public function isActorReaction(): bool
    {
        return $this->source === self::SOURCE_ACTOR;
    }

    public function isSystemEvent(): bool
    {
        return $this->source === self::SOURCE_SYSTEM;
    }

    public function getSourceLabel(): string
    {
        return match($this->source) {
            self::SOURCE_PLAYER => 'Игрок',
            self::SOURCE_ACTOR => 'Актор',
            self::SOURCE_SYSTEM => 'Система',
            default => 'Неизвестно',
        };
    }
}
