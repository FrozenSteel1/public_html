<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Индексы для таблицы game_histories
        Schema::table('game_histories', function (Blueprint $table) {
            $table->index(['game_id', 'created_at']);
            $table->index('event_id');
        });

        // Индексы для таблицы effects
        Schema::table('effects', function (Blueprint $table) {
            $table->index('event_id');
            $table->index('effect_type_id');
        });

        // Индексы для таблицы choices
        Schema::table('choices', function (Blueprint $table) {
            $table->index('scene_id');
            $table->index('event_id');
        });

        // Индексы для таблицы scenes
        Schema::table('scenes', function (Blueprint $table) {
            $table->index(['scenario_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::table('game_histories', function (Blueprint $table) {
            $table->dropIndex(['game_id', 'created_at']);
            $table->dropIndex('event_id');
        });

        Schema::table('effects', function (Blueprint $table) {
            $table->dropIndex('event_id');
            $table->dropIndex('effect_type_id');
        });

        Schema::table('choices', function (Blueprint $table) {
            $table->dropIndex('scene_id');
            $table->dropIndex('event_id');
        });

        Schema::table('scenes', function (Blueprint $table) {
            $table->dropIndex(['scenario_id', 'order']);
        });
    }
};
