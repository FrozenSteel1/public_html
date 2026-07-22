<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preset extends Model
{
    protected $fillable = [
        'scenario_id',
        'difficulty',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }
}
