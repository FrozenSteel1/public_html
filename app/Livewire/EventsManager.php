<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EffectType;
use Livewire\Component;
use Livewire\WithPagination;

class EventsManager extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $editingId = null;

    // Для эффектов: теперь каждый эффект имеет ключ и значение вместо JSON
    public $effects = [];

    public $showForm = false;
    public $search = '';

    // Предустановленные ключи для эффектов
    public $effectKeys = [
        'Институциональная устойчивость',
        'Управляемость аппарата',
        'Конфликтная напряженность',
        'Публичная легитимность',
        'Доверие к процедурам',
        'Риск управленческого сбоя',
        'Горизонт устойчивости',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'effects.*.effect_type_id' => 'required|exists:effect_types,id',
        'effects.*.key' => 'nullable|string',
        'effects.*.value' => 'nullable|string',
    ];

    public function render()
    {
        $events = Event::when($this->search, function($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->with(['effects', 'effects.effectType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $effectTypes = EffectType::all();

        return view('livewire.events-manager', [
            'events' => $events,
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
        $event = Event::with('effects')->findOrFail($id);
        $this->editingId = $event->id;
        $this->name = $event->name;
        $this->description = $event->description;

        // Преобразование эффектов: извлекаем key и value из effect_data
        $this->effects = $event->effects->map(function($effect) {
            $effectData = $this->jsonToArray($effect->effect_data);

            // Если effect_data содержит простые пары ключ-значение
            $key = '';
            $value = '';

            if (is_array($effectData)) {
                if (isset($effectData['key'])) {
                    $key = $effectData['key'];
                    $value = $effectData['value'] ?? '';
                } elseif (count($effectData) > 0) {
                    // Если это массив с одним элементом
                    $firstKey = array_key_first($effectData);
                    if (is_string($firstKey)) {
                        $key = $firstKey;
                        $value = $effectData[$firstKey];
                    }
                }
            }

            return [
                'id' => $effect->id,
                'effect_type_id' => $effect->effect_type_id,
                'key' => $key,
                'value' => $value,
            ];
        })->toArray();

        $this->showForm = true;
    }

    /**
     * Добавление эффекта с возможностью указать ключ
     */
    public function addEffect($key = '')
    {
        $this->effects[] = [
            'id' => null,
            'effect_type_id' => '',
            'key' => $key,
            'value' => '',
        ];
    }

    public function removeEffect($index)
    {
        if (isset($this->effects[$index])) {
            unset($this->effects[$index]);
            $this->effects = array_values($this->effects);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            $event = Event::findOrFail($this->editingId);
            $event->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            // Синхронизация эффектов
            $existingEffectIds = $event->effects->pluck('id')->toArray();
            $updatedEffectIds = [];

            foreach ($this->effects as $effect) {
                // Формируем effect_data как простой JSON с ключом и значением
                $effectData = $this->buildEffectData($effect['key'] ?? '', $effect['value'] ?? '');

                $effectDataArray = [
                    'effect_type_id' => $effect['effect_type_id'],
                    'effect_data' => $effectData,
                ];

                if (isset($effect['id']) && $effect['id']) {
                    $existingEffect = $event->effects()->find($effect['id']);
                    if ($existingEffect) {
                        $existingEffect->update($effectDataArray);
                        $updatedEffectIds[] = $effect['id'];
                    }
                } else {
                    $newEffect = $event->effects()->create($effectDataArray);
                    $updatedEffectIds[] = $newEffect->id;
                }
            }

            // Удаление неиспользуемых эффектов
            $toDelete = array_diff($existingEffectIds, $updatedEffectIds);
            if (!empty($toDelete)) {
                $event->effects()->whereIn('id', $toDelete)->delete();
            }

            session()->flash('message', 'Событие успешно обновлено.');
        } else {
            $event = Event::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            foreach ($this->effects as $effect) {
                if (!empty($effect['effect_type_id'])) {
                    $effectData = $this->buildEffectData($effect['key'] ?? '', $effect['value'] ?? '');

                    $event->effects()->create([
                        'effect_type_id' => $effect['effect_type_id'],
                        'effect_data' => $effectData,
                    ]);
                }
            }

            session()->flash('message', 'Событие успешно создано.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Event::findOrFail($id)->delete();
        session()->flash('message', 'Событие успешно удалено.');
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
        $this->effects = [];
        $this->resetValidation();
    }

    /**
     * Построение effect_data из ключа и значения
     */
    private function buildEffectData($key, $value): string
    {
        $data = [];

        if (!empty($key)) {
            $data['key'] = $key;
        }

        if (!empty($value)) {
            $data['value'] = $value;
        }

        // Если есть и ключ и значение, сохраняем как пару
        if (!empty($key) && isset($value)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        // Если только значение, сохраняем как есть
        if (empty($key) && !empty($value)) {
            return json_encode(['value' => $value], JSON_UNESCAPED_UNICODE);
        }

        // Если только ключ
        if (!empty($key) && empty($value)) {
            return json_encode(['key' => $key], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Преобразование JSON строки в массив
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
}
