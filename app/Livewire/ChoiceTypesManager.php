<?php

namespace App\Livewire;

use App\Models\ChoiceType;
use Livewire\Component;

class ChoiceTypesManager extends Component
{
    public $name = '';
    public $editingId = null;
    public $showForm = false;

    public function render()
    {
        $types = ChoiceType::withCount('choices')
            ->orderBy('id')
            ->get();

        return view('livewire.choice-types-manager', [
            'types' => $types,
        ])
            ->layout('layouts.app')
            ->title('Типы выборов');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $type = ChoiceType::findOrFail($id);
        $this->editingId = $type->id;
        $this->name = $type->name;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:choice_types,name,' . ($this->editingId ?: 'null'),
        ]);

        if ($this->editingId) {
            ChoiceType::findOrFail($this->editingId)->update(['name' => $this->name]);
            session()->flash('message', 'Тип выбора успешно обновлён.');
        } else {
            ChoiceType::create(['name' => $this->name]);
            session()->flash('message', 'Тип выбора успешно создан.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $type = ChoiceType::findOrFail($id);
        $usedCount = $type->choices()->count();
        $type->delete();

        session()->flash('message', $usedCount > 0
            ? "Тип выбора удалён. У {$usedCount} выборов тип сброшен в NULL."
            : 'Тип выбора успешно удалён.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->editingId = null;
        $this->resetValidation();
    }
}
