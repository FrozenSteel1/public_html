<?php

namespace App\Livewire;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class CompaniesManager extends Component
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
        $companies = Company::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.companies-manager', [
            'companies' => $companies,
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
        $company = Company::findOrFail($id);
        $this->editingId = $company->id;
        $this->name = $company->name;
        $this->description = $company->description;
        $this->difficulty = $company->difficulty;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $company = Company::findOrFail($this->editingId);
            $company->update([
                'name' => $this->name,
                'description' => $this->description,
                'difficulty' => $this->difficulty,
            ]);
            session()->flash('message', 'Компания успешно обновлена.');
        } else {
            Company::create([
                'name' => $this->name,
                'description' => $this->description,
                'difficulty' => $this->difficulty,
            ]);
            session()->flash('message', 'Компания успешно создана.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Company::findOrFail($id)->delete();
        session()->flash('message', 'Компания успешно удалена.');
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
