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
use App\Controllers\TestController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/game/play/{scenarioId}', GamePlay::class)
        ->name('game.play')
        ->where('scenarioId', '[0-9]+');
});
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/companies', CompaniesManager::class)->name('companies');
    Route::get('/scenarios', ScenariosManager::class)->name('scenarios');
    Route::get('/scenes', ScenesManager::class)->name('scenes');
    Route::get('/events', EventsManager::class)->name('events');
    Route::get('/effect-types', EffectTypesManager::class)->name('effect-types');
    Route::get('/actors', ActorsManager::class)->name('actors');
    Route::get('/company-scenarios', CompanyScenarioManager::class)->name('company-scenarios');
    Route::get('/presets', PresetsManager::class)->name('presets');
    Route::get('/games', GamesManager::class)->name('games');

});
Route::middleware(['auth'])->group(function () {
    // Страница выбора сценария
    Route::get('/scenarios', ScenarioSelector::class)
        ->name('scenarios');

    // Страница игры
    Route::get('/game/play/{scenarioId}', GamePlay::class)
        ->name('game.play')
        ->where('scenarioId', '[0-9]+');
});
Route::middleware(['auth'])->prefix('test')->group(function () {
    Route::get('/game/{gameId}', [TestController::class, 'showGame']);
    Route::get('/state/{gameId}', [TestController::class, 'showState']);
});

