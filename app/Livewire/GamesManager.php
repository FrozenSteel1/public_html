<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\User;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class GamesManager extends Component
{
    use WithPagination;

    public $user_id = '';
    public $company_id = '';
    public $status = 'started';
    public $editingId = null;

    // Для истории игры
    public $gameHistoryEvents = [];

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'company_id' => 'required|exists:companies,id',
        'status' => 'required|in:started,in_progress,completed,failed',
    ];

    public function render()
    {
        $games = Game::with(['user', 'company'])
            ->when($this->search, function($query) {
                return $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('company', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $users = User::all();
        $companies = Company::all();

        return view('livewire.games-manager', [
            'games' => $games,
            'users' => $users,
            'companies' => $companies,
            'statuses' => ['started', 'in_progress', 'completed', 'failed']
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $game = Game::with('gameHistories.event')->findOrFail($id);
        $this->editingId = $game->id;
        $this->user_id = $game->user_id;
        $this->company_id = $game->company_id;
        $this->status = $game->status;

        // Загрузка истории игры
        $this->gameHistoryEvents = $game->gameHistories->pluck('event_id')->toArray();

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $game = Game::findOrFail($this->editingId);
            $game->update([
                'user_id' => $this->user_id,
                'company_id' => $this->company_id,
                'status' => $this->status,
            ]);

            // Обновление истории игры (если нужно)
            // Здесь можно добавить логику синхронизации game_histories

            session()->flash('message', 'Игра успешно обновлена.');
        } else {
            $game = Game::create([
                'user_id' => $this->user_id,
                'company_id' => $this->company_id,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Игра успешно создана.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Game::findOrFail($id)->delete();
        session()->flash('message', 'Игра успешно удалена.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->user_id = '';
        $this->company_id = '';
        $this->status = 'started';
        $this->editingId = null;
        $this->gameHistoryEvents = [];
        $this->resetValidation();
    }
}
