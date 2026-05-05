<?php

namespace App\Livewire;

use App\Models\EffectType;
use Livewire\Component;
use Livewire\WithPagination;

class EffectTypesManager extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:effect_types,name',
        'description' => 'nullable|string',
    ];

    public function render()
    {
        $effectTypes = EffectType::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->withCount('effects')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.effect-types-manager', [
            'effectTypes' => $effectTypes,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $effectType = EffectType::findOrFail($id);
        $this->editingId = $effectType->id;
        $this->name = $effectType->name;
        $this->description = $effectType->description;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];

        // Уникальность только при создании
        if (!$this->editingId) {
            $rules['name'] .= '|unique:effect_types,name';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $effectType = EffectType::findOrFail($this->editingId);
            $effectType->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Тип эффекта успешно обновлен.');
        } else {
            EffectType::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Тип эффекта успешно создан.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        EffectType::findOrFail($id)->delete();
        session()->flash('message', 'Тип эффекта успешно удален.');
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
        $this->editingId = null;
        $this->resetValidation();
    }
}
