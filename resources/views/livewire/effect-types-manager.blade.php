<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление типами эффектов
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
                           placeholder="Поиск типов эффектов...">
                </div>

                @if($showForm)
                    <div class="bg-gray-50 shadow-md rounded px-8 pt-6 pb-8 mb-4 border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ $editingId ? 'Редактирование типа эффекта' : 'Новый тип эффекта' }}
                        </h3>

                        <form wire:submit="save">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Название</label>
                                <input wire:model="name" type="text" id="name"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                       placeholder="Название типа эффекта">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Описание</label>
                                <textarea wire:model="description" id="description" rows="3"
                                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                          placeholder="Описание типа эффекта"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                            Создать тип эффекта
                        </button>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Название</th>
                            <th class="py-3 px-6 text-left">Описание</th>
                            <th class="py-3 px-6 text-center">Используется</th>
                            <th class="py-3 px-6 text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                        @foreach($effectTypes as $effectType)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6">{{ $effectType->id }}</td>
                                <td class="py-3 px-6 font-medium">{{ $effectType->name }}</td>
                                <td class="py-3 px-6">{{ Str::limit($effectType->description, 50) }}</td>
                                <td class="py-3 px-6 text-center">
                                        <span class="bg-purple-200 text-purple-600 py-1 px-3 rounded-full text-xs">
                                            {{ $effectType->effects_count }} раз
                                        </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="edit({{ $effectType->id }})"
                                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs mr-2">
                                        Редактировать
                                    </button>
                                    <button wire:click="delete({{ $effectType->id }})"
                                            wire:confirm="Вы уверены, что хотите удалить этот тип эффекта?"
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
                    {{ $effectTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
