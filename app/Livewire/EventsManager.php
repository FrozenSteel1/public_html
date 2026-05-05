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

    // Для эффектов
    public $effects = [];
    public $newEffectTypeId = '';
    public $newEffectData = [];

    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'effects.*.effect_type_id' => 'required|exists:effect_types,id',
        'effects.*.effect_data' => 'required|array',
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
            return [
                'id' => $effect->id,
                'effect_type_id' => $effect->effect_type_id,
                'effect_data' => $effect->effect_data,
            ];
        })->toArray();

        $this->showForm = true;
    }

    public function addEffect()
    {
        $this->effects[] = [
            'id' => null,
            'effect_type_id' => '',
            'effect_data' => [],
        ];
    }

    public function removeEffect($index)
    {
        unset($this->effects[$index]);
        $this->effects = array_values($this->effects);
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
                if (isset($effect['id'])) {
                    $existingEffect = $event->effects()->find($effect['id']);
                    if ($existingEffect) {
                        $existingEffect->update([
                            'effect_type_id' => $effect['effect_type_id'],
                            'effect_data' => $effect['effect_data'],
                        ]);
                        $updatedEffectIds[] = $effect['id'];
                    }
                } else {
                    $newEffect = $event->effects()->create([
                        'effect_type_id' => $effect['effect_type_id'],
                        'effect_data' => $effect['effect_data'],
                    ]);
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
                $event->effects()->create([
                    'effect_type_id' => $effect['effect_type_id'],
                    'effect_data' => $effect['effect_data'],
                ]);
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
        $this->newEffectTypeId = '';
        $this->newEffectData = [];
        $this->resetValidation();
    }
}
