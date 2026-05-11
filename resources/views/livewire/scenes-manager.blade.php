<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление сценами
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
                           placeholder="Поиск сцен...">

                    <button wire:click="create" type="button"
                            style="background-color: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                        + Создать сцену
                    </button>
                </div>

                <!-- Форма -->
                @if($showForm)
                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">
                            {{ $editingId ? 'Редактирование сцены' : 'Новая сцена' }}
                        </h3>

                        <form wire:submit.prevent="save">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; font-weight: bold; margin-bottom: 4px;">Сценарий *</label>
                                    <select wire:model="scenario_id"
                                            style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                        <option value="">Выберите сценарий</option>
                                        @foreach($scenarios as $scenario)
                                            <option value="{{ $scenario->id }}">{{ $scenario->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('scenario_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label style="display: block; font-weight: bold; margin-bottom: 4px;">Порядок</label>
                                    <input wire:model="order" type="number" min="0"
                                           style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                    @error('order') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Заголовок *</label>
                                <input wire:model="title" type="text"
                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                @error('title') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Ситуация *</label>
                                <textarea wire:model="situation" rows="4"
                                          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"></textarea>
                                @error('situation') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <!-- Дополнительные данные с автоподстановкой -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: bold;">Дополнительные данные</span>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        @foreach($predefinedKeys as $predefinedKey)
                                            <button wire:click="addAdditionalData('{{ $predefinedKey }}')" type="button"
                                                    style="background-color: #6366f1; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer; white-space: nowrap;">
                                                + {{ $predefinedKey }}
                                            </button>
                                        @endforeach
                                        <button wire:click="addAdditionalData" type="button"
                                                style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer; white-space: nowrap;">
                                            + Свой ключ
                                        </button>
                                    </div>
                                </div>

                                @if(is_array($additional_data) && count($additional_data) > 0)
                                    @foreach($additional_data as $index => $data)
                                        <div style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                            <!-- Поле ключа с автоподстановкой -->
                                            <div style="flex: 1; position: relative;">
                                                <input wire:model.live="additional_data.{{ $index }}.key"
                                                       type="text"
                                                       list="predefined-keys-list"
                                                       style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                       placeholder="Выберите или впишите ключ"
                                                       autocomplete="off">
                                                <datalist id="predefined-keys-list">
                                                    @foreach($predefinedKeys as $predefinedKey)
                                                        <option value="{{ $predefinedKey }}">
                                                    @endforeach
                                                </datalist>
                                            </div>

                                            <!-- Поле значения: если ключ "Актор" - показываем select с акторами -->
                                            <div style="flex: 2;">
                                                @if(isset($data['key']) && $data['key'] === 'Актор')
                                                    <select wire:model="additional_data.{{ $index }}.value"
                                                            style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px; background-color: #eff6ff;">
                                                        <option value="">Выберите актора</option>
                                                        @foreach($actorsList as $actor)
                                                            <option value="{{ $actor['id'] }}">{{ $actor['name'] }} (ID: {{ $actor['id'] }})</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input wire:model="additional_data.{{ $index }}.value"
                                                           type="text"
                                                           style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                           placeholder="Значение">
                                                @endif
                                            </div>

                                            <button wire:click="removeAdditionalData({{ $index }})" type="button"
                                                    style="background-color: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                ✕
                                            </button>
                                        </div>

                                        <!-- Превью выбранного актора -->
                                        @if(isset($data['key']) && $data['key'] === 'Актор' && !empty($data['value']))
                                            @php
                                                $selectedActor = collect($actorsList)->firstWhere('id', (int)$data['value']);
                                            @endphp
                                            @if($selectedActor)
                                                <div style="margin: -4px 0 8px 0; padding: 4px 10px; background-color: #dbeafe; border-radius: 4px; font-size: 12px; color: #1e40af;">
                                                    Выбран: <strong>{{ $selectedActor['name'] }}</strong>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 12px; text-align: center; background: #f9fafb; border-radius: 4px;">
                                        Нет дополнительных данных. Нажмите на кнопку с нужным ключом или "Свой ключ"
                                    </div>
                                @endif
                            </div>
                            <!-- Выборы (без изменений) -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: bold;">Выборы (Choices)</span>
                                    <button wire:click="addChoice" type="button"
                                            style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                        + Добавить выбор
                                    </button>
                                </div>

                                @if(is_array($choices) && count($choices) > 0)
                                    @foreach($choices as $index => $choice)
                                        <div style="padding: 12px; margin-bottom: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                <span style="font-weight: 600; font-size: 14px;">Выбор #{{ $index + 1 }}</span>
                                                <button wire:click="removeChoice({{ $index }})" type="button"
                                                        style="background-color: #ef4444; color: white; padding: 4px 8px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                                    ✕ Удалить
                                                </button>
                                            </div>

                                            <div style="margin-bottom: 8px;">
                                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 2px;">Описание *</label>
                                                <input wire:model="choices.{{ $index }}.description" type="text"
                                                       style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                       placeholder="Описание выбора">
                                                @error('choices.'.$index.'.description')
                                                <span style="color: red; font-size: 11px;">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                <div>
                                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 2px;">Событие *</label>
                                                    <select wire:model="choices.{{ $index }}.event_id"
                                                            style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;">
                                                        <option value="">Выберите событие</option>
                                                        @foreach($events as $event)
                                                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('choices.'.$index.'.event_id')
                                                    <span style="color: red; font-size: 11px;">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 2px;">Порядок</label>
                                                    <input wire:model="choices.{{ $index }}.order" type="number" min="0"
                                                           style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 12px; text-align: center;">
                                        Нет выборов. Нажмите "Добавить выбор"
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

                <!-- Таблица сцен -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb;">
                        <thead>
                        <tr style="background-color: #f3f4f6; color: #4b5563; text-transform: uppercase; font-size: 14px;">
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">ID</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Сценарий</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Заголовок</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Порядок</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Выборы</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Действия</th>
                        </tr>
                        </thead>
                        <tbody style="color: #4b5563; font-size: 14px;">
                        @forelse($scenes as $scene)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px;">{{ $scene->id }}</td>
                                <td style="padding: 12px;">{{ $scene->scenario->name ?? 'N/A' }}</td>
                                <td style="padding: 12px; font-weight: 500;">{{ $scene->title }}</td>
                                <td style="padding: 12px; text-align: center;">{{ $scene->order }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @php
                                        $choicesCount = is_countable($scene->choices) ? count($scene->choices) : 0;
                                    @endphp
                                    @if($choicesCount > 0)
                                        <span style="background-color: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ $choicesCount }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 8px;">
                                        <button wire:click="edit({{ $scene->id }})"
                                                style="background-color: #f59e0b; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            ✏️ Ред.
                                        </button>
                                        <button wire:click="delete({{ $scene->id }})"
                                                wire:confirm="Удалить сцену '{{ $scene->title }}'?"
                                                style="background-color: #ef4444; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            🗑️ Уд.
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    Сцены не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $scenes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
