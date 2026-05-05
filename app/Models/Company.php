<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'description',
        'difficulty',
    ];

    // Связь с таблицей games (одна компания может иметь много игр)
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    // Связь многие ко многим с scenarios через таблицу company_scenario
    public function scenarios(): BelongsToMany
    {
        return $this->belongsToMany(Scenario::class, 'company_scenario')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('company_scenario.order');
    }

    // Связь с company_scenario (прямой доступ к промежуточной таблице)
    public function companyScenarios(): HasMany
    {
        return $this->hasMany(CompanyScenario::class);
    }
}
