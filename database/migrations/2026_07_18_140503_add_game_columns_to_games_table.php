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
        Schema::table('games', function (Blueprint $table) {
            // Добавляем поле текущей сцены
            $table->foreignId('current_scene_id')
                ->nullable()
                ->after('company_id')
                ->constrained('scenes')
                ->onDelete('set null');

            // Добавляем поле сложности
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'expert', 'custom'])
                ->default('easy')
                ->after('current_scene_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['current_scene_id']);
            $table->dropColumn(['current_scene_id', 'difficulty']);
        });
    }
};
