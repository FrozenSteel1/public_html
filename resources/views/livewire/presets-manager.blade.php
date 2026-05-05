<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление предустановками
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mb-4">
                    <input wire:model.live.debounce.300ms="search" type="text"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Поиск предустановок...">
                </div>

                @if($showForm)
                    <div class="bg-gray-50 shadow-md rounded px-8 pt-6 pb-8 mb-4 border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ $editingId ? 'Редактирование предустановки' : 'Новая предустановка' }}
                        </h3>

                        <form wire:submit="save">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="scenario_id">Сценарий</label>
                                <select wire:model="scenario_id" id="scenario_id"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Выберите сценарий</option>
                                    @foreach($scenarios as $scenario)
                                        <option value="{{ $scenario->id }}">{{ $scenario->name }}</option>
                                    @endforeach
                                </select>
                                @error('scenario_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="difficulty">Сложность</label>
                                <select wire:model="difficulty" id="difficulty"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($difficulties as $difficulty)
                                        <option value="{{ $difficulty }}">{{ ucfirst($difficulty) }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-gray-700 text-sm font-bold">Настройки предустановки</label>
                                    <button wire:click="addSetting" type="button"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Добавить настройку
                                    </button>
                                </div>

                                @foreach($settings as $index => $setting)
                                    <div class="grid grid-cols-2 gap-4 mb-2">
                                        <input wire:model="settings.{{ $index }}.key" type="text"
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline"
                                               placeholder="Ключ">
                                        <div class="flex gap-2">
                                            <input wire:model="settings.{{ $index }}.value" type="text"
                                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline"
                                                   placeholder="Значение">
                                            <button wire:click="removeSetting({{ $index }})" type="button"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                X
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                @error('settings') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="submit"
                                        style="background-color: #2563eb !important;
               color: white !important;
               padding: 10px 20px !important;
               border: 2px solid #1d4ed8 !important;
               border-radius: 6px !important;
               font-weight: bold !important;
               font-size: 14px !important;
               cursor: pointer !important;
               box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;"
                                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                                        onmouseout="this.style.backgroundColor='#2563eb'">
                                    {{ $editingId ? 'Обновить' : 'Создать' }}
                                </button>
                                <button wire:click="cancel" type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if(!$showForm)
                    <div class="mb-4">
                        <button wire:click="create" style="background-color: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                            Создать предустановку
                        </button>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Сценарий</th>
                            <th class="py-3 px-6 text-left">Сложность</th>
                            <th class="py-3 px-6 text-center">Настройки</th>
                            <th class="py-3 px-6 text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                        @foreach($presets as $preset)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6">{{ $preset->id }}</td>
                                <td class="py-3 px-6 font-medium">{{ $preset->scenario->name ?? 'N/A' }}</td>
                                <td class="py-3 px-6">
                                        <span class="bg-{{ $preset->difficulty === 'hard' || $preset->difficulty === 'expert' ? 'red' : 'green' }}-200
                                                   text-{{ $preset->difficulty === 'hard' || $preset->difficulty === 'expert' ? 'red' : 'green' }}-600
                                                   py-1 px-3 rounded-full text-xs">
                                            {{ ucfirst($preset->difficulty) }}
                                        </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                        <span class="bg-blue-200 text-blue-600 py-1 px-3 rounded-full text-xs">
                                            {{ is_array($preset->settings) ? count($preset->settings) : 0 }}
                                        </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="edit({{ $preset->id }})"
                                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs mr-2">
                                        Редактировать
                                    </button>
                                    <button wire:click="delete({{ $preset->id }})"
                                            wire:confirm="Вы уверены, что хотите удалить эту предустановку?"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $presets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
