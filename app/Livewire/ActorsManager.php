<?php

namespace App\Livewire;

use App\Models\Actor;
use Livewire\Component;
use Livewire\WithPagination;

class ActorsManager extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $settings = [];
    public $triggers = [];
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'settings' => 'nullable|array',
        'triggers' => 'nullable|array',
    ];

    public function render()
    {
        $actors = Actor::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.actors-manager', [
            'actors' => $actors,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $actor = Actor::findOrFail($id);
        $this->editingId = $actor->id;
        $this->name = $actor->name;
        $this->description = $actor->description;

        // Правильное преобразование JSON в массив
        $this->settings = $this->jsonToArray($actor->settings);
        $this->triggers = $this->jsonToArray($actor->triggers);

        $this->showForm = true;
    }

    public function addSetting()
    {
        $this->settings[] = ['key' => '', 'value' => ''];
    }

    public function removeSetting($index)
    {
        if (isset($this->settings[$index])) {
            unset($this->settings[$index]);
            $this->settings = array_values($this->settings);
        }
    }

    public function addTrigger()
    {
        $this->triggers[] = ['key' => '', 'value' => ''];
    }

    public function removeTrigger($index)
    {
        if (isset($this->triggers[$index])) {
            unset($this->triggers[$index]);
            $this->triggers = array_values($this->triggers);
        }
    }

    public function save()
    {
        $this->validate();

        // Подготовка данных для сохранения
        $settingsData = $this->arrayToJson($this->settings);
        $triggersData = $this->arrayToJson($this->triggers);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'settings' => $settingsData,
            'triggers' => $triggersData,
        ];

        if ($this->editingId) {
            $actor = Actor::findOrFail($this->editingId);
            $actor->update($data);
            session()->flash('message', 'Актор успешно обновлен.');
        } else {
            Actor::create($data);
            session()->flash('message', 'Актор успешно создан.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $actor = Actor::findOrFail($id);
        $actor->delete();
        session()->flash('message', 'Актор успешно удален.');
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
        $this->settings = [];
        $this->triggers = [];
        $this->editingId = null;
        $this->resetValidation();
    }

    /**
     * Преобразование JSON строки или массива в массив
     */
    private function jsonToArray($data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_string($data) && !empty($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Преобразование массива в JSON строку для сохранения
     */
    private function arrayToJson($data): ?string
    {
        if (empty($data)) {
            return null;
        }

        // Фильтруем пустые значения
        $filtered = array_filter($data, function($item) {
            return is_array($item) && (!empty($item['key']) || !empty($item['value']));
        });

        if (empty($filtered)) {
            return null;
        }

        return json_encode(array_values($filtered), JSON_UNESCAPED_UNICODE);
    }
}
