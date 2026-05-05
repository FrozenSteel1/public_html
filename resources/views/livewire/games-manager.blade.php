<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление играми
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
                           placeholder="Поиск игр по пользователю или компании...">
                </div>

                @if($showForm)
                    <div class="bg-gray-50 shadow-md rounded px-8 pt-6 pb-8 mb-4 border">
                        <h3 class="text-lg font-semibold mb-4">
                            {{ $editingId ? 'Редактирование игры' : 'Новая игра' }}
                        </h3>

                        <form wire:submit="save">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">Пользователь</label>
                                    <select wire:model="user_id" id="user_id"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Выберите пользователя</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="company_id">Компания</label>
                                    <select wire:model="company_id" id="company_id"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Выберите компанию</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Статус игры</label>
                                <select wire:model="status" id="status"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">
                                            @switch($status)
                                                @case('started') Начата @break
                                                @case('in_progress') В процессе @break
                                                @case('completed') Завершена @break
                                                @case('failed') Провалена @break
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                            Создать игру
                        </button>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Пользователь</th>
                            <th class="py-3 px-6 text-left">Компания</th>
                            <th class="py-3 px-6 text-center">Статус</th>
                            <th class="py-3 px-6 text-left">Начата</th>
                            <th class="py-3 px-6 text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                        @foreach($games as $game)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6">{{ $game->id }}</td>
                                <td class="py-3 px-6 font-medium">{{ $game->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-6">{{ $game->company->name ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-center">
                                    @switch($game->status)
                                        @case('started')
                                            <span class="bg-gray-200 text-gray-600 py-1 px-3 rounded-full text-xs">Начата</span>
                                            @break
                                        @case('in_progress')
                                            <span class="bg-blue-200 text-blue-600 py-1 px-3 rounded-full text-xs">В процессе</span>
                                            @break
                                        @case('completed')
                                            <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">Завершена</span>
                                            @break
                                        @case('failed')
                                            <span class="bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs">Провалена</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="py-3 px-6">{{ $game->created_at->format('d.m.Y H:i') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="edit({{ $game->id }})"
                                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs mr-2">
                                        Редактировать
                                    </button>
                                    <button wire:click="delete({{ $game->id }})"
                                            wire:confirm="Вы уверены, что хотите удалить эту игру?"
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
                    {{ $games->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
