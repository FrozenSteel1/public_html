<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление компаниями
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

                <!-- Верхняя панель с поиском и кнопкой создать -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex-1 mr-4">
                        <input wire:model.live.debounce.300ms="search" type="text"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               placeholder="Поиск компаний...">
                    </div>
                    @if(!$showForm)
                        <button wire:click="create"
                                style="background-color: #3B82F6; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; border: none; cursor: pointer;"
                                class="hover:bg-blue-700">
                            + Создать компанию
                        </button>
                    @endif
                </div>

                <!-- Форма создания/редактирования -->
                @if($showForm)
                    <div class="bg-gray-50 shadow-md rounded px-8 pt-6 pb-8 mb-4 border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ $editingId ? 'Редактирование компании' : 'Новая компания' }}
                        </h3>

                        <form wire:submit="save">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Название
                                </label>
                                <input wire:model="name" type="text" id="name"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                       placeholder="Название компании">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                    Описание
                                </label>
                                <textarea wire:model="description" id="description" rows="3"
                                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                          placeholder="Описание компании"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="difficulty">
                                    Сложность
                                </label>
                                <select wire:model="difficulty" id="difficulty"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($difficulties as $difficulty)
                                        <option value="{{ $difficulty }}">{{ ucfirst($difficulty) }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                <button wire:click="cancel" type="button"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Таблица компаний -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Название</th>
                            <th class="py-3 px-6 text-left">Сложность</th>
                            <th class="py-3 px-6 text-left">Создано</th>
                            <th class="py-3 px-6 text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                        @foreach($companies as $company)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6">{{ $company->id }}</td>
                                <td class="py-3 px-6">{{ $company->name }}</td>
                                <td class="py-3 px-6">
                                        <span class="bg-{{ $company->difficulty === 'hard' || $company->difficulty === 'expert' ? 'red' : 'green' }}-200
                                                   text-{{ $company->difficulty === 'hard' || $company->difficulty === 'expert' ? 'red' : 'green' }}-600
                                                   py-1 px-3 rounded-full text-xs">
                                            {{ ucfirst($company->difficulty) }}
                                        </span>
                                </td>
                                <td class="py-3 px-6">{{ $company->created_at->format('d.m.Y H:i') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="edit({{ $company->id }})"
                                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs mr-2">
                                        Редактировать
                                    </button>
                                    <button wire:click="delete({{ $company->id }})"
                                            wire:confirm="Вы уверены, что хотите удалить эту компанию?"
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
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
