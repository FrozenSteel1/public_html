<?php

namespace App\Livewire;

use App\Models\Scenario;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class ScenariosManager extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $difficulty = 'medium';
    public $company_id = '';
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    // Список компаний для автоподстановки
    public $companiesList = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'difficulty' => 'required|in:easy,medium,hard,expert,custom',
        'company_id' => 'nullable|exists:companies,id',
    ];

    public function mount()
    {
        $this->loadCompanies();
    }

    public function loadCompanies()
    {
        $this->companiesList = Company::select('id', 'name', 'difficulty')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function render()
    {
        $scenarios = Scenario::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->with(['company', 'scenes', 'presets'])
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
        $this->company_id = $scenario->company_id ?? '';
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'difficulty' => $this->difficulty,
            'company_id' => $this->company_id ?: null,
        ];

        if ($this->editingId) {
            $scenario = Scenario::findOrFail($this->editingId);
            $scenario->update($data);
            session()->flash('message', 'Сценарий успешно обновлен.');
        } else {
            Scenario::create($data);
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
        $this->company_id = '';
        $this->editingId = null;
        $this->resetValidation();
    }
}
