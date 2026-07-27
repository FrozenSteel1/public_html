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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем колонку role с значениями по умолчанию 'player'
            $table->enum('role', ['admin', 'player'])
                ->default('player')
                ->after('email')
                ->comment('Роль пользователя: admin - администратор, player - игрок');

            // Добавляем индекс для быстрого поиска по роли
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
