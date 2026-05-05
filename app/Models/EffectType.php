<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EffectType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Связь с таблицей effects (один тип эффекта может быть использован во многих эффектах)
    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }
}
