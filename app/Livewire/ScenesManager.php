<?php

namespace App\Livewire;

use App\Models\Scene;
use App\Models\Scenario;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class ScenesManager extends Component
{
    use WithPagination;

    public $scenario_id = '';
    public $title = '';
    public $situation = '';
    public $additional_data = [];
    public $order = 0;
    public $editingId = null;

    // Для выборов
    public $choices = [];

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'scenario_id' => 'required|exists:scenarios,id',
        'title' => 'required|string|max:255',
        'situation' => 'required|string',
        'order' => 'required|integer|min:0',
        'choices.*.description' => 'required|string',
        'choices.*.event_id' => 'required|exists:events,id',
    ];

    public function render()
    {
        $scenes = Scene::when($this->search, function($query) {
            return $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('situation', 'like', '%' . $this->search . '%');
        })
            ->with(['scenario', 'choices', 'choices.event'])
            ->orderBy('scenario_id')
            ->orderBy('order')
            ->paginate(10);

        $scenarios = Scenario::all();
        $events = Event::all();

        return view('livewire.scenes-manager', [
            'scenes' => $scenes,
            'scenarios' => $scenarios,
            'events' => $events,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $scene = Scene::with('choices')->findOrFail($id);
        $this->editingId = $scene->id;
        $this->scenario_id = $scene->scenario_id;
        $this->title = $scene->title;
        $this->situation = $scene->situation;
        $this->additional_data = $this->jsonToArray($scene->additional_data);
        $this->order = $scene->order;

        // Загрузка существующих выборов
        $this->choices = $scene->choices->map(function($choice) {
            return [
                'id' => $choice->id,
                'description' => $choice->description,
                'event_id' => $choice->event_id,
                'conditions' => $this->jsonToArray($choice->conditions),
                'order' => $choice->order,
            ];
        })->toArray();

        // Если выборов нет, инициализируем пустой массив
        if (empty($this->choices)) {
            $this->choices = [];
        }

        $this->showForm = true;
    }

    public function addChoice()
    {
        $this->choices[] = [
            'id' => null,
            'description' => '',
            'event_id' => '',
            'conditions' => [],
            'order' => count($this->choices),
        ];
    }

    public function removeChoice($index)
    {
        if (isset($this->choices[$index])) {
            unset($this->choices[$index]);
            $this->choices = array_values($this->choices);
        }
    }

    public function addAdditionalData()
    {
        $this->additional_data[] = ['key' => '', 'value' => ''];
    }

    public function removeAdditionalData($index)
    {
        if (isset($this->additional_data[$index])) {
            unset($this->additional_data[$index]);
            $this->additional_data = array_values($this->additional_data);
        }
    }

    public function save()
    {
        $this->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'title' => 'required|string|max:255',
            'situation' => 'required|string',
            'order' => 'required|integer|min:0',
        ]);

        $additionalDataJson = $this->arrayToJson($this->additional_data);

        if ($this->editingId) {
            $scene = Scene::findOrFail($this->editingId);
            $scene->update([
                'scenario_id' => $this->scenario_id,
                'title' => $this->title,
                'situation' => $this->situation,
                'additional_data' => $additionalDataJson,
                'order' => $this->order,
            ]);

            // Синхронизация выборов
            $existingChoiceIds = $scene->choices->pluck('id')->toArray();
            $updatedChoiceIds = [];

            foreach ($this->choices as $choice) {
                $choiceData = [
                    'description' => $choice['description'],
                    'event_id' => $choice['event_id'],
                    'conditions' => $this->arrayToJson($choice['conditions'] ?? []),
                    'order' => $choice['order'] ?? 0,
                ];

                if (isset($choice['id']) && $choice['id']) {
                    $existingChoice = $scene->choices()->find($choice['id']);
                    if ($existingChoice) {
                        $existingChoice->update($choiceData);
                        $updatedChoiceIds[] = $choice['id'];
                    }
                } else {
                    $newChoice = $scene->choices()->create($choiceData);
                    $updatedChoiceIds[] = $newChoice->id;
                }
            }

            // Удаление неиспользуемых выборов
            $toDelete = array_diff($existingChoiceIds, $updatedChoiceIds);
            if (!empty($toDelete)) {
                $scene->choices()->whereIn('id', $toDelete)->delete();
            }

            session()->flash('message', 'Сцена успешно обновлена.');
        } else {
            $scene = Scene::create([
                'scenario_id' => $this->scenario_id,
                'title' => $this->title,
                'situation' => $this->situation,
                'additional_data' => $additionalDataJson,
                'order' => $this->order,
            ]);

            // Создание выборов
            foreach ($this->choices as $choice) {
                if (!empty($choice['description']) && !empty($choice['event_id'])) {
                    $scene->choices()->create([
                        'description' => $choice['description'],
                        'event_id' => $choice['event_id'],
                        'conditions' => $this->arrayToJson($choice['conditions'] ?? []),
                        'order' => $choice['order'] ?? 0,
                    ]);
                }
            }

            session()->flash('message', 'Сцена успешно создана.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Scene::findOrFail($id)->delete();
        session()->flash('message', 'Сцена успешно удалена.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->scenario_id = '';
        $this->title = '';
        $this->situation = '';
        $this->additional_data = [];
        $this->order = 0;
        $this->editingId = null;
        $this->choices = [];
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

        // Если это простой массив без ключей
        if (isset($data[0]) && !is_array($data[0])) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        // Если это ассоциативный массив или массив с ключами key/value
        $filtered = array_filter($data, function($item) {
            if (is_array($item)) {
                return !empty($item['key']) || !empty($item['value']);
            }
            return !empty($item);
        });

        if (empty($filtered)) {
            return null;
        }

        return json_encode(array_values($filtered), JSON_UNESCAPED_UNICODE);
    }
}
