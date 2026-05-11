<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление событиями
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
                           placeholder="Поиск событий...">

                    <button wire:click="create" type="button"
                            style="background-color: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                        + Создать событие
                    </button>
                </div>

                <!-- Форма -->
                @if($showForm)
                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">
                            {{ $editingId ? 'Редактирование события' : 'Новое событие' }}
                        </h3>

                        <form wire:submit.prevent="save">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Название события *</label>
                                <input wire:model="name" type="text"
                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                       placeholder="Название события">
                                @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Описание</label>
                                <textarea wire:model="description" rows="3"
                                          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                          placeholder="Описание события"></textarea>
                                @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <!-- Эффекты -->
                            <div style="margin-bottom: 20px; padding: 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                    <span style="font-weight: bold; font-size: 15px;">⚡ Эффекты события</span>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <!-- Кнопки быстрого добавления с предустановленными ключами -->
                                        @foreach($effectKeys as $effectKey)
                                            <button wire:click="addEffect('{{ $effectKey }}')" type="button"
                                                    style="background-color: #7c3aed; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                                + {{ $effectKey }}
                                            </button>
                                        @endforeach
                                        <!-- Кнопка добавления пустого эффекта -->
                                        <button wire:click="addEffect" type="button"
                                                style="background-color: #10b981; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; white-space: nowrap;">
                                            + Свой ключ
                                        </button>
                                    </div>
                                </div>

                                @if(is_array($effects) && count($effects) > 0)
                                    @foreach($effects as $index => $effect)
                                        <div style="padding: 12px; margin-bottom: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                <span style="font-weight: 600; font-size: 14px; color: #4b5563;">
                                                    Эффект #{{ $index + 1 }}
                                                    @if(!empty($effect['key']))
                                                        - <span style="color: #7c3aed;">{{ $effect['key'] }}</span>
                                                    @endif
                                                </span>
                                                <button wire:click="removeEffect({{ $index }})" type="button"
                                                        style="background-color: #ef4444; color: white; padding: 4px 10px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                                    ✕ Удалить
                                                </button>
                                            </div>

                                            <!-- Тип эффекта -->
                                            <div style="margin-bottom: 10px;">
                                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                    Тип эффекта *
                                                </label>
                                                <select wire:model="effects.{{ $index }}.effect_type_id"
                                                        style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; background-color: white;">
                                                    <option value="">Выберите тип эффекта</option>
                                                    @foreach($effectTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('effects.'.$index.'.effect_type_id')
                                                <span style="color: red; font-size: 11px;">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Ключ и Значение вместо JSON -->
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                <!-- Ключ с автоподстановкой -->
                                                <div>
                                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                        Ключ
                                                    </label>
                                                    <input wire:model="effects.{{ $index }}.key"
                                                           type="text"
                                                           list="effect-keys-list"
                                                           style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                           placeholder="Выберите или впишите ключ"
                                                           autocomplete="off">
                                                    <datalist id="effect-keys-list">
                                                        @foreach($effectKeys as $effectKey)
                                                            <option value="{{ $effectKey }}">
                                                        @endforeach
                                                    </datalist>
                                                </div>

                                                <!-- Значение -->
                                                <div>
                                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #4b5563;">
                                                        Значение
                                                    </label>
                                                    <input wire:model="effects.{{ $index }}.value" type="text"
                                                           style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;"
                                                           placeholder="Значение">
                                                </div>
                                            </div>

                                            <!-- Превью данных -->
                                            @if(!empty($effect['key']) || !empty($effect['value']))
                                                <div style="margin-top: 8px; padding: 6px 10px; background-color: #f3e8ff; border-radius: 4px; font-size: 12px; color: #6b21a8;">
                                                    <strong>Данные эффекта:</strong>
                                                    @if(!empty($effect['key']) && !empty($effect['value']))
                                                        {{ $effect['key'] }}: {{ $effect['value'] }}
                                                    @elseif(!empty($effect['key']))
                                                        {{ $effect['key'] }}
                                                    @elseif(!empty($effect['value']))
                                                        {{ $effect['value'] }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div style="color: #9ca3af; font-size: 14px; padding: 20px; text-align: center; background: #f9fafb; border-radius: 4px;">
                                        <p style="margin-bottom: 12px;">Нет эффектов.</p>
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

                <!-- Таблица событий -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb;">
                        <thead>
                        <tr style="background-color: #f3f4f6; color: #4b5563; text-transform: uppercase; font-size: 14px;">
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">ID</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Название</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Описание</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Эффекты</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Действия</th>
                        </tr>
                        </thead>
                        <tbody style="color: #4b5563; font-size: 14px;">
                        @forelse($events as $event)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px;">{{ $event->id }}</td>
                                <td style="padding: 12px; font-weight: 500;">{{ $event->name }}</td>
                                <td style="padding: 12px;">{{ \Illuminate\Support\Str::limit($event->description, 50) }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @php
                                        $effectsCount = is_countable($event->effects) ? count($event->effects) : 0;
                                    @endphp
                                    @if($effectsCount > 0)
                                        <span style="background-color: #ede9fe; color: #5b21b6; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ $effectsCount }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 8px;">
                                        <button wire:click="edit({{ $event->id }})"
                                                style="background-color: #f59e0b; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            ✏️ Ред.
                                        </button>
                                        <button wire:click="delete({{ $event->id }})"
                                                wire:confirm="Удалить событие '{{ $event->name }}'?"
                                                style="background-color: #ef4444; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            🗑️ Уд.
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    События не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
