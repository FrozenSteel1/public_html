<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Game;
use App\Models\Scenario;
use App\Models\Actor;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->stats = [
            'users' => User::count(),
            'games' => Game::count(),
            'scenarios' => Scenario::count(),
            'actors' => Actor::count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('layouts.app')
            ->title('Админ-панель');
    }
}
