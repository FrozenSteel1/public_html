<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Scenario;
use App\Models\CompanyScenario;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyScenarioManager extends Component
{
    use WithPagination;

    public $company_id = '';
    public $scenario_id = '';
    public $order = 0;
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'company_id' => 'required|exists:companies,id',
        'scenario_id' => 'required|exists:scenarios,id',
        'order' => 'required|integer|min:0',
    ];

    public function render()
    {
        $companyScenarios = CompanyScenario::with(['company', 'scenario'])
            ->when($this->search, function($query) {
                return $query->whereHas('company', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('scenario', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('company_id')
            ->orderBy('order')
            ->paginate(10);

        $companies = Company::all();
        $scenarios = Scenario::all();

        return view('livewire.company-scenario-manager', [
            'companyScenarios' => $companyScenarios,
            'companies' => $companies,
            'scenarios' => $scenarios,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $companyScenario = CompanyScenario::findOrFail($id);
        $this->editingId = $companyScenario->id;
        $this->company_id = $companyScenario->company_id;
        $this->scenario_id = $companyScenario->scenario_id;
        $this->order = $companyScenario->order;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = $this->rules;

        // Проверка уникальности при создании
        if (!$this->editingId) {
            $rules['company_id'] .= '|unique:company_scenario,company_id,NULL,id,scenario_id,' . $this->scenario_id;
        }

        $this->validate($rules);

        if ($this->editingId) {
            $companyScenario = CompanyScenario::findOrFail($this->editingId);
            $companyScenario->update([
                'company_id' => $this->company_id,
                'scenario_id' => $this->scenario_id,
                'order' => $this->order,
            ]);
            session()->flash('message', 'Связь компании и сценария успешно обновлена.');
        } else {
            CompanyScenario::create([
                'company_id' => $this->company_id,
                'scenario_id' => $this->scenario_id,
                'order' => $this->order,
            ]);
            session()->flash('message', 'Связь компании и сценария успешно создана.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        CompanyScenario::findOrFail($id)->delete();
        session()->flash('message', 'Связь успешно удалена.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->company_id = '';
        $this->scenario_id = '';
        $this->order = 0;
        $this->editingId = null;
        $this->resetValidation();
    }
}
