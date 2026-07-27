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

    // Для эффектов: теперь каждый эффект имеет поля в зависимости от типа
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

        $this->effects = $event->effects->map(function($effect) {
            $effectData = $this->jsonToArray($effect->effect_data);
            $effectTypeId = (int) $effect->effect_type_id;

            $result = [
                'id' => $effect->id,
                'effect_type_id' => $effectTypeId,
                'key' => '',
                'value' => '',
                'message' => '',
                'delay' => 2,
                'type' => 'info',
            ];

            // В зависимости от типа заполняем нужные поля
            switch ($effectTypeId) {
                case 12: // Сообщение
                    $result['message'] = $effectData['message'] ?? '';
                    $result['type'] = $effectData['type'] ?? 'info';
                    break;
                case 13: // Отложенное сообщение
                    $result['message'] = $effectData['message'] ?? '';
                    $result['delay'] = $effectData['delay'] ?? 2;
                    $result['type'] = $effectData['type'] ?? 'info';
                    break;
                default:
                    $result['key'] = $effectData['key'] ?? '';
                    $result['value'] = $effectData['value'] ?? '';
                    break;
            }

            return $result;
        })->toArray();

        $this->showForm = true;
    }

    /**
     * Добавление эффекта
     */
    /**
     * Добавление эффекта
     */
    /**
     * Добавление эффекта
     */
    public function addEffect($presetType = 'default')
    {
        $effect = [
            'id' => null,
            'effect_type_id' => '',
            'key' => '',
            'value' => '',
            'message' => '',
            'delay' => 2,
            'type' => 'info',
            'preset_type' => $presetType,
        ];

        // Автоматически подставляем тип эффекта в зависимости от presetType
        switch ($presetType) {
            case 'message':
                $effect['effect_type_id'] = 12; // Сообщение
                break;
            case 'delayed':
                $effect['effect_type_id'] = 13; // Отложенное сообщение
                break;
            case 'default':
                // Оставляем пустым, пользователь выберет сам
                break;
            default:
                // Если передан ключ (например, "Институциональная устойчивость")
                // оставляем effect_type_id пустым, но заполняем key
                $effect['key'] = $presetType;
                break;
        }

        $this->effects[] = $effect;
    }

    public function removeEffect($index)
    {
        if (isset($this->effects[$index])) {
            unset($this->effects[$index]);
            $this->effects = array_values($this->effects);
        }
    }

    /**
     * Обновление полей при изменении типа эффекта
     */
    public function updatedEffects($value, $key)
    {
        // Если изменился effect_type_id у какого-то эффекта
        if (str_ends_with($key, '.effect_type_id')) {
            $index = explode('.', $key)[0];
            $effectTypeId = (int) $value;

            // Сбрасываем все поля
            $this->effects[$index]['key'] = '';
            $this->effects[$index]['value'] = '';
            $this->effects[$index]['message'] = '';
            $this->effects[$index]['delay'] = 2;
            $this->effects[$index]['type'] = 'info';
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
                if (empty($effect['effect_type_id'])) {
                    continue;
                }

                // Формируем effect_data в зависимости от типа
                $effectData = $this->buildEffectData($effect);

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
                    $effectData = $this->buildEffectData($effect);

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
     * Построение effect_data в зависимости от типа эффекта
     */
    private function buildEffectData($effect): string
    {
        $effectTypeId = (int) ($effect['effect_type_id'] ?? 0);
        $data = [];

        switch ($effectTypeId) {
            case 12: // Сообщение
                if (!empty($effect['message'])) {
                    $data['message'] = $effect['message'];
                }
                if (!empty($effect['type'])) {
                    $data['type'] = $effect['type'];
                }
                break;

            case 13: // Отложенное сообщение
                if (!empty($effect['message'])) {
                    $data['message'] = $effect['message'];
                }
                if (!empty($effect['delay'])) {
                    $data['delay'] = (int) $effect['delay'];
                }
                if (!empty($effect['type'])) {
                    $data['type'] = $effect['type'];
                }
                break;

            default: // Обычные эффекты (повышение/снижение показателей)
                if (!empty($effect['key'])) {
                    $data['key'] = $effect['key'];
                }
                if (!empty($effect['value'])) {
                    $data['value'] = $effect['value'];
                }
                break;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
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

    /**
     * Проверить, является ли тип эффекта "сообщением"
     */
    public function isMessageType($effectTypeId): bool
    {
        return in_array((int) $effectTypeId, [12, 13]);
    }

    /**
     * Получить название типа для отображения
     */
    public function getEffectTypeLabel($effectTypeId): string
    {
        $types = [
            12 => 'Сообщение',
            13 => 'Отложенное сообщение',
        ];
        return $types[(int) $effectTypeId] ?? 'Обычный';
    }
}
