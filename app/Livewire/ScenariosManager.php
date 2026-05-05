<?php

namespace App\Livewire;

use App\Models\Scenario;
use Livewire\Component;
use Livewire\WithPagination;

class ScenariosManager extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $difficulty = 'medium';
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'difficulty' => 'required|in:easy,medium,hard,expert,custom',
    ];

    public function render()
    {
        $scenarios = Scenario::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->withCount(['scenes', 'presets'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.scenarios-manager', [
            'scenarios' => $scenarios,
            'difficulties' => ['easy', 'medium', 'hard', 'expert', 'custom']
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $scenario = Scenario::findOrFail($id);
        $this->editingId = $scenario->id;
        $this->name = $scenario->name;
        $this->description = $scenario->description;
        $this->difficulty = $scenario->difficulty;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $scenario = Scenario::findOrFail($this->editingId);
            $scenario->update([
                'name' => $this->name,
                'description' => $this->description,
                'difficulty' => $this->difficulty,
            ]);
            session()->flash('message', 'Сценарий успешно обновлен.');
        } else {
            Scenario::create([
                'name' => $this->name,
                'description' => $this->description,
                'difficulty' => $this->difficulty,
            ]);
            session()->flash('message', 'Сценарий успешно создан.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Scenario::findOrFail($id)->delete();
        session()->flash('message', 'Сценарий успешно удален.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->difficulty = 'medium';
        $this->editingId = null;
        $this->resetValidation();
    }
}
