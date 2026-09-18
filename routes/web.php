<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CompaniesManager;
use App\Livewire\ScenariosManager;
use App\Livewire\ScenesManager;
use App\Livewire\EventsManager;
use App\Livewire\EffectTypesManager;
use App\Livewire\ActorsManager;
use App\Livewire\CompanyScenarioManager;
use App\Livewire\PresetsManager;
use App\Livewire\GamesManager;
use App\Livewire\GamePlay;
use App\Livewire\ScenarioSelector;
use App\Livewire\GameResults;
use App\Livewire\PlayerDashboard;
use App\Livewire\AdminDashboard;
use App\Livewire\ChoiceTypesManager;

Route::get('/', function () {
    return view('welcome');
});

// ========== МАРШРУТЫ ДЛЯ АВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ ==========
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Старый дашборд JetStream - перенаправляем на дашборд игрока
    Route::get('/dashboard', function () {
        return redirect()->route('player.dashboard');
    })->name('dashboard');
});

// ========== АДМИН-ПАНЕЛЬ (ТОЛЬКО ДЛЯ АДМИНОВ) ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/companies', CompaniesManager::class)->name('companies');
    Route::get('/scenarios', ScenariosManager::class)->name('scenarios');
    Route::get('/scenes', ScenesManager::class)->name('scenes');
    Route::get('/choice-types', ChoiceTypesManager::class)->name('choice-types');
    Route::get('/events', EventsManager::class)->name('events');
    Route::get('/effect-types', EffectTypesManager::class)->name('effect-types');
    Route::get('/actors', ActOrsManager::class)->name('actors');
    Route::get('/company-scenarios', CompanyScenarioManager::class)->name('company-scenarios');
    Route::get('/presets', PresetsManager::class)->name('presets');
    Route::get('/games', GamesManager::class)->name('games');
});

// ========== ИГРОВЫЕ МАРШРУТЫ (ДЛЯ ВСЕХ АВТОРИЗОВАННЫХ) ==========
Route::middleware(['auth'])->group(function () {
    // Дашборд игрока (главная страница после входа)
    Route::get('/dashboard', PlayerDashboard::class)->name('player.dashboard');

    // Страница выбора сценария
    Route::get('/scenarios', ScenarioSelector::class)->name('scenarios');

    // Страница со списком игр (дублирует дашборд, оставляем для обратной совместимости)
    Route::get('/my-games', PlayerDashboard::class)->name('user.games');

    // Результаты игры
    Route::get('/game/results/{gameId}', GameResults::class)
        ->name('game.results')
        ->where('gameId', '[0-9]+');

    // Новая игра (с параметром difficulty — опциональный)
    Route::get('/game/play/{scenarioId}/{difficulty?}', GamePlay::class)
        ->name('game.play')
        ->where('scenarioId', '[0-9]+')
        ->where('difficulty', 'easy|medium|hard|expert|custom');

    // Продолжить игру
    Route::get('/game/continue/{gameId}', GamePlay::class)
        ->name('game.continue')
        ->where('gameId', '[0-9]+');
});
