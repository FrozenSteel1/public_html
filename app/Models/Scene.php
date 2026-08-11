<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Scene extends Model
{
    protected $fillable = [
        'scenario_id',
        'order',
        'title',
        'situation',
        'additional_data',
    ];

    // УБИРАЕМ $casts
    // protected $casts = ['additional_data' => 'array'];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class)->orderBy('order');
    }

    protected function additionalData(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $data = $value;
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (json_last_error() === JSON_ERROR_NONE) $data = $decoded;
                }
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (json_last_error() === JSON_ERROR_NONE) $data = $decoded;
                }
                return is_array($data) ? $data : [];
            }
        );
    }

    public function getActors(): array
    {
        $data = $this->additional_data; // Уже гарантированно массив!
        return $data['actors'] ?? [];
    }
}
