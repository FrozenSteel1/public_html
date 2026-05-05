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
                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Описание</label>
                                <textarea wire:model="description" rows="3"
                                          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"></textarea>
                                @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <!-- Настройки -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: bold;">Настройки (Settings)</span>
                                    <button wire:click="addSetting" type="button"
                                            style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                        + Добавить настройку
                                    </button>
                                </div>

                                @if(count($settings) > 0)
                                    @foreach($settings as $index => $setting)
                                        <div style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                            <input wire:model="settings.{{ $index }}.key" type="text"
                                                   style="flex: 1; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                   placeholder="Ключ">
                                            <input wire:model="settings.{{ $index }}.value" type="text"
                                                   style="flex: 2; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                   placeholder="Значение">
                                            <button wire:click="removeSetting({{ $index }})" type="button"
                                                    style="background-color: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 12px; text-align: center;">
                                        Нет настроек. Нажмите "Добавить настройку"
                                    </div>
                                @endif
                            </div>

                            <!-- Триггеры -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: bold;">Триггеры (Triggers)</span>
                                    <button wire:click="addTrigger" type="button"
                                            style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                        + Добавить триггер
                                    </button>
                                </div>

                                @if(count($triggers) > 0)
                                    @foreach($triggers as $index => $trigger)
                                        <div style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                            <input wire:model="triggers.{{ $index }}.key" type="text"
                                                   style="flex: 1; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                   placeholder="Ключ">
                                            <input wire:model="triggers.{{ $index }}.value" type="text"
                                                   style="flex: 2; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
                                                   placeholder="Значение">
                                            <button wire:click="removeTrigger({{ $index }})" type="button"
                                                    style="background-color: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 12px; text-align: center;">
                                        Нет триггеров. Нажмите "Добавить триггер"
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

                <!-- Таблица -->
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
