<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление сценариями
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
                           placeholder="Поиск сценариев...">

                    <button wire:click="create" type="button"
                            style="background-color: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                        + Создать сценарий
                    </button>
                </div>

                <!-- Форма -->
                @if($showForm)
                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">
                            {{ $editingId ? 'Редактирование сценария' : 'Новый сценарий' }}
                        </h3>

                        <form wire:submit.prevent="save">
                            <!-- Название и компания -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; font-weight: bold; margin-bottom: 4px;">Название *</label>
                                    <input wire:model="name" type="text"
                                           style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                           placeholder="Название сценария">
                                    @error('name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>

                                <!-- Выбор компании -->
                                <div>
                                    <label style="display: block; font-weight: bold; margin-bottom: 4px;">
                                        🏢 Компания
                                    </label>
                                    <select wire:model="company_id"
                                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white;">
                                        <option value="">Без компании</option>
                                        @foreach($companiesList as $company)
                                            <option value="{{ $company['id'] }}">
                                                {{ $company['name'] }}
                                                ({{ ucfirst($company['difficulty']) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror

                                    <!-- Превью выбранной компании -->
                                    @if(!empty($company_id))
                                        @php
                                            $selectedCompany = collect($companiesList)->firstWhere('id', (int)$company_id);
                                        @endphp
                                        @if($selectedCompany)
                                            <div style="margin-top: 4px; padding: 4px 10px; background-color: #dbeafe; border-radius: 4px; font-size: 12px; color: #1e40af;">
                                                Выбрана: <strong>{{ $selectedCompany['name'] }}</strong>
                                                (Сложность: {{ ucfirst($selectedCompany['difficulty']) }})
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Описание</label>
                                <textarea wire:model="description" rows="3"
                                          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px;"
                                          placeholder="Описание сценария"></textarea>
                                @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 4px;">Сложность</label>
                                <select wire:model="difficulty"
                                        style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white;">
                                    @foreach($difficulties as $d)
                                        <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
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

                <!-- Таблица сценариев -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb;">
                        <thead>
                        <tr style="background-color: #f3f4f6; color: #4b5563; text-transform: uppercase; font-size: 14px;">
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">ID</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Название</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Компания</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">Сложность</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Сцены</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Предустановки</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">Действия</th>
                        </tr>
                        </thead>
                        <tbody style="color: #4b5563; font-size: 14px;">
                        @forelse($scenarios as $scenario)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px;">{{ $scenario->id }}</td>
                                <td style="padding: 12px; font-weight: 500;">{{ $scenario->name }}</td>
                                <td style="padding: 12px;">
                                    @if($scenario->company)
                                        <span style="background-color: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                                {{ $scenario->company->name }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px;">
                                        <span style="padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                            @if($scenario->difficulty === 'easy') background-color: #d1fae5; color: #065f46;
                                            @elseif($scenario->difficulty === 'medium') background-color: #fef3c7; color: #92400e;
                                            @elseif($scenario->difficulty === 'hard') background-color: #fed7aa; color: #9a3412;
                                            @elseif($scenario->difficulty === 'expert') background-color: #fecaca; color: #991b1b;
                                            @else background-color: #e0e7ff; color: #3730a3;
                                            @endif">
                                            {{ ucfirst($scenario->difficulty) }}
                                        </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($scenario->scenes_count > 0)
                                        <span style="background-color: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ $scenario->scenes_count }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($scenario->presets_count > 0)
                                        <span style="background-color: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                                {{ $scenario->presets_count }}
                                            </span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 8px;">
                                        <button wire:click="edit({{ $scenario->id }})"
                                                style="background-color: #f59e0b; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            ✏️ Ред.
                                        </button>
                                        <button wire:click="delete({{ $scenario->id }})"
                                                wire:confirm="Удалить сценарий '{{ $scenario->name }}'?"
                                                style="background-color: #ef4444; color: white; padding: 4px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            🗑️ Уд.
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    Сценарии не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $scenarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
