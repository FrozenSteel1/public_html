<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scenario extends Model
{
    protected $fillable = [
        'name',
        'description',
        'difficulty',
    ];

    // Связь с таблицей scenes (один сценарий имеет много сцен)
    public function scenes(): HasMany
    {
        return $this->hasMany(Scene::class)->orderBy('order');
    }

    // Связь многие ко многим с companies через таблицу company_scenario
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_scenario')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('company_scenario.order');
    }

    // Связь с company_scenario (прямой доступ к промежуточной таблице)
    public function companyScenarios(): HasMany
    {
        return $this->hasMany(CompanyScenario::class);
    }

    // Связь с таблицей presets (один сценарий может иметь много предустановок)
    public function presets(): HasMany
    {
        return $this->hasMany(Preset::class);
    }
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
