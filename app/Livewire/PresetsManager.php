<?php

namespace App\Livewire;

use App\Models\Preset;
use App\Models\Scenario;
use App\Models\ParameterDefinition;
use Livewire\Component;
use Livewire\WithPagination;

class PresetsManager extends Component
{
    use WithPagination;

    public $scenario_id = '';
    public $difficulty = 'medium';
    public $settings = [];
    public $editingId = null;

    public $showForm = false;
    public $search = '';

    // Список параметров для автоподстановки
    public $parameterNames = [];

    protected $rules = [
        'scenario_id' => 'required|exists:scenarios,id',
        'difficulty' => 'required|in:easy,medium,hard,expert,custom',
        'settings' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->loadParameterNames();
    }

    public function loadParameterNames()
    {
        $this->parameterNames = ParameterDefinition::orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function render()
    {
        $presets = Preset::with('scenario')
            ->when($this->search, function($query) {
                return $query->whereHas('scenario', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('scenario_id')
            ->orderBy('difficulty')
            ->paginate(10);

        $scenarios = Scenario::all();

        return view('livewire.presets-manager', [
            'presets' => $presets,
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
        $preset = Preset::findOrFail($id);
        $this->editingId = $preset->id;
        $this->scenario_id = $preset->scenario_id;
        $this->difficulty = $preset->difficulty;

        // Получаем настройки
        $settings = $preset->settings;

        // Если это строка - декодируем
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        // Если после декодирования всё ещё строка (двойное экранирование)
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        // Если получили массив - используем его
        $this->settings = is_array($settings) ? $settings : [];

        $this->showForm = true;
    }

    public function addSetting($key = '')
    {
        $this->settings[] = ['key' => $key, 'value' => ''];
    }

    public function removeSetting($index)
    {
        unset($this->settings[$index]);
        $this->settings = array_values($this->settings);
    }

    public function save()
    {
        $rules = $this->rules;

        if (!$this->editingId) {
            $rules['scenario_id'] .= '|unique:presets,scenario_id,NULL,id,difficulty,' . $this->difficulty;
        }

        $this->validate($rules);

        // Преобразование массива настроек в JSON с сохранением русских букв
        $settingsData = json_encode($this->settings, JSON_UNESCAPED_UNICODE);

        if ($this->editingId) {
            $preset = Preset::findOrFail($this->editingId);
            $preset->update([
                'scenario_id' => $this->scenario_id,
                'difficulty' => $this->difficulty,
                'settings' => $settingsData,
            ]);
            session()->flash('message', 'Предустановка успешно обновлена.');
        } else {
            Preset::create([
                'scenario_id' => $this->scenario_id,
                'difficulty' => $this->difficulty,
                'settings' => $settingsData,
            ]);
            session()->flash('message', 'Предустановка успешно создана.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Preset::findOrFail($id)->delete();
        session()->flash('message', 'Предустановка успешно удалена.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->scenario_id = '';
        $this->difficulty = 'medium';
        $this->settings = [];
        $this->editingId = null;
        $this->resetValidation();
    }
}
