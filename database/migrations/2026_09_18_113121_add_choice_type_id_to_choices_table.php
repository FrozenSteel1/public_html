<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('choices', function (Blueprint $table) {
            $table->foreignId('choice_type_id')
                ->nullable()                      // поле опционально, по умолчанию NULL
                ->after('event_id')               // рядом с event_id (MySQL; на SQLite модификатор игнорируется)
                ->constrained('choice_types')     // FK на choice_types.id
                ->nullOnDelete();                 // при удалении типа — обнуляем, а не роняем выборы
        });
    }

    public function down(): void
    {
        Schema::table('choices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('choice_type_id'); // снимает FK и удаляет колонку
        });
    }
};
