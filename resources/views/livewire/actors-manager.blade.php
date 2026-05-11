<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление акторами
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session()->has('message'))
                    <div style="background-color: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Поиск и кнопка создать -->
                <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                    <input wire:model.live.debounce.300ms="search" type="text"
                           style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                           placeholder="Поиск акторов...">

                    <button wire:click="create" type="button"
                            style="background-color: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                        + Создать актора
                    </button>
                </div>

                <!-- Форма -->
                @if($showForm)
                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">
                            {{ $editingId ? 'Редактирование актора' : 'Новый актор' }}
                        </h3>

                        <form wire:submit.prevent="save">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Имя актора *</label>
                                <input wire:model="name" type="text"
                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                       placeholder="Введите имя актора">
                                @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Описание</label>
                                <textarea wire:model="description" rows="3"
                                          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                          placeholder="Описание актора"></textarea>
                                @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <!-- Настройки -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                    <span style="font-weight: bold; font-size: 15px;">⚙️ Настройки (Settings)</span>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <!-- Кнопки быстрого добавления с предустановленными ключами -->
                                        @foreach($settingKeys as $settingKey)
                                            <button wire:click="addSetting('{{ $settingKey }}')" type="button"
                                                    style="background-color: #0891b2; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                                + {{ $settingKey }}
                                            </button>
                                        @endforeach
                                        <!-- Кнопка добавления пустой настройки -->
                                        <button wire:click="addSetting" type="button"
                                                style="background-color: #10b981; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                            + Свой ключ
                                        </button>
                                    </div>
                                </div>

                                @if(is_array($settings) && count($settings) > 0)
                                    @foreach($settings as $index => $setting)
                                        <div style="padding: 10px; margin-bottom: 8px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <!-- Ключ с автоподстановкой -->
                                                <div style="flex: 1;">
                                                    <input wire:model="settings.{{ $index }}.key"
                                                           type="text"
                                                           list="setting-keys-list"
                                                           style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                           placeholder="Выберите или впишите ключ"
                                                           autocomplete="off">
                                                    <datalist id="setting-keys-list">
                                                        @foreach($settingKeys as $settingKey)
                                                            <option value="{{ $settingKey }}">
                                                        @endforeach
                                                    </datalist>
                                                </div>

                                                <!-- Значение -->
                                                <input wire:model="settings.{{ $index }}.value" type="text"
                                                       style="flex: 2; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                       placeholder="Значение">

                                                <!-- Кнопка удаления -->
                                                <button wire:click="removeSetting({{ $index }})" type="button"
                                                        style="background-color: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                    ✕
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 20px; text-align: center; background: #f9fafb; border-radius: 4px;">
                                        <p style="margin-bottom: 12px;">Нет настроек.</p>
                                        <p style="font-size: 12px;">Нажмите на кнопку с нужным ключом или "Свой ключ" для добавления</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Триггеры -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                    <span style="font-weight: bold; font-size: 15px;">🔔 Триггеры (Triggers)</span>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <!-- Кнопки быстрого добавления с предустановленными ключами -->
                                        @foreach($triggerKeys as $triggerKey)
                                            <button wire:click="addTrigger('{{ $triggerKey }}')" type="button"
                                                    style="background-color: #6366f1; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                                + {{ $triggerKey }}
                                            </button>
                                        @endforeach
                                        <!-- Кнопка добавления пустого триггера -->
                                        <button wire:click="addTrigger" type="button"
                                                style="background-color: #10b981; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                            + Свой ключ
                                        </button>
                                    </div>
                                </div>

                                @if(is_array($triggers) && count($triggers) > 0)
                                    @foreach($triggers as $index => $trigger)
                                        <div style="padding: 12px; margin-bottom: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                <span style="font-weight: 600; font-size: 14px; color: #4b5563;">
                                                    Триггер #{{ $index + 1 }}
                                                    @if(!empty($trigger['key']))
                                                        - <span style="color: #6366f1;">{{ $trigger['key'] }}</span>
                                                    @endif
                                                </span>
                                                <button wire:click="removeTrigger({{ $index }})" type="button"
                                                        style="background-color: #ef4444; color: white; padding: 4px 10px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                                    ✕ Удалить
                                                </button>
                                            </div>

                                            <!-- Ключ триггера с автоподстановкой -->
                                            <div style="margin-bottom: 10px;">
                                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                    Ключ триггера
                                                </label>
                                                <input wire:model="triggers.{{ $index }}.key"
                                                       type="text"
                                                       list="trigger-keys-list"
                                                       style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                       placeholder="Выберите или впишите ключ триггера"
                                                       autocomplete="off">
                                                <datalist id="trigger-keys-list">
                                                    @foreach($triggerKeys as $triggerKey)
                                                        <option value="{{ $triggerKey }}">
                                                    @endforeach
                                                </datalist>
                                            </div>

                                            <!-- Значение триггера -->
                                            <div style="margin-bottom: 10px;">
                                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                    Значение триггера
                                                </label>
                                                <input wire:model="triggers.{{ $index }}.value" type="text"
                                                       style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                       placeholder="Значение триггера">
                                            </div>

                                            <!-- Событие -->
                                            <div>
                                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                    🎯 Событие, которое активирует триггер
                                                </label>
                                                <select wire:model="triggers.{{ $index }}.event_id"
                                                        style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; background-color: white;">
                                                    <option value="">Выберите событие</option>
                                                    @foreach($eventsList as $event)
                                                        <option value="{{ $event['id'] }}">{{ $event['name'] }} (ID: {{ $event['id'] }})</option>
                                                    @endforeach
                                                </select>

                                                @if(!empty($trigger['event_id']))
                                                    @php
                                                        $selectedEvent = collect($eventsList)->firstWhere('id', (int)$trigger['event_id']);
                                                    @endphp
                                                    @if($selectedEvent)
                                                        <div style="margin-top: 4px; padding: 4px 10px; background-color: #e0e7ff; border-radius: 4px; font-size: 12px; color: #3730a3;">
                                                            Будет вызвано событие: <strong>{{ $selectedEvent['name'] }}</strong>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 20px; text-align: center; background: #f9fafb; border-radius: 4px;">
                                        <p style="margin-bottom: 12px;">Нет триггеров.</p>
                                        <p style="font-size: 12px;">Нажмите на кнопку с нужным ключом или "Свой ключ" для добавления</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Кнопки -->
                            <div style="display: flex; gap: 12px;">
                                <button type="submit"
                                        style="background-color: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                                    {{ $editingId ? '✓ Обновить' : '✓ Создать' }}
                                </button>
                                <button wire:click="cancel" type="button"
                                        style="background-color: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                                    ✗ Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Таблица акторов -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb;">
                        <thead>
                        <tr style="background-color: #f3f4f6; color: #4b5563; text-transform: uppercase; font-size: 14px;">
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">ID</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Имя</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Описание</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Настройки</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Триггеры</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Действия</th>
                        </tr>
                        </thead>
                        <tbody style="color: #4b5563; font-size: 14px;">
                        @forelse($actors as $actor)
                            @php
                                $actorSettings = is_array($actor->settings) ? $actor->settings : json_decode($actor->settings, true);
                                $actorTriggers = is_array($actor->triggers) ? $actor->triggers : json_decode($actor->triggers, true);
                            @endphp
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px;">{{ $actor->id }}</td>
                                <td style="padding: 12px; font-weight: 500;">{{ $actor->name }}</td>
                                <td style="padding: 12px;">{{ \Illuminate\Support\Str::limit($actor->description, 50) }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @if(is_array($actorSettings) && count($actorSettings) > 0)
                                        <span style="background-color: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ count($actorSettings) }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if(is_array($actorTriggers) && count($actorTriggers) > 0)
                                        <span style="background-color: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ count($actorTriggers) }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 8px;">
                                        <button wire:click="edit({{ $actor->id }})"
                                                style="background-color: #f59e0b; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            ✏️ Ред.
                                        </button>
                                        <button wire:click="delete({{ $actor->id }})"
                                                wire:confirm="Удалить актора '{{ $actor->name }}'?"
                                                style="background-color: #ef4444; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            🗑️ Уд.
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    Акторы не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $actors->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
