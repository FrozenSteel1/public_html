<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $fillable = [
        'name',
        'description',
        'settings',
        'triggers',
    ];

    protected $casts = [
        'settings' => 'array',  // Автоматическое преобразование JSON в массив
        'triggers' => 'array',   // Автоматическое преобразование JSON в массив
    ];

    // В текущей структуре БД нет прямых связей с другими таблицами
    // Если понадобятся связи, их можно добавить позже
}
