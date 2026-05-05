<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyScenario extends Model
{
    protected $fillable = [
        'company_id',
        'scenario_id',
        'order',
    ];

    // Связь с таблицей companies (принадлежит компании)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Связь с таблицей scenarios (принадлежит сценарию)
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }
}
