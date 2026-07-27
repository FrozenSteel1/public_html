<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_histories', function (Blueprint $table) {
            // Добавляем колонку scene_id
            $table->unsignedBigInteger('scene_id')
                ->nullable()
                ->after('event_id')
                ->comment('ID сцены, на которой произошло событие');

            // Добавляем внешний ключ
            $table->foreign('scene_id')
                ->references('id')
                ->on('scenes')
                ->onDelete('set null');

            // Добавляем индекс для быстрого поиска
            $table->index(['game_id', 'scene_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_histories', function (Blueprint $table) {
            $table->dropForeign(['scene_id']);
            $table->dropColumn('scene_id');
        });
    }
};
