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
            // Добавляем колонку source с возможными значениями:
            // player - событие инициировано игроком (через Choice)
            // actor - событие инициировано актором (через Trigger)
            // system - системное событие (таймер, автоматические реакции и т.д.)
            $table->enum('source', ['player', 'actor', 'system'])
                ->default('system')
                ->after('event_id')
                ->comment('Источник события: player - игрок, actor - актор, system - система');

            // Добавляем индекс для быстрого поиска
            $table->index(['game_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_histories', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
