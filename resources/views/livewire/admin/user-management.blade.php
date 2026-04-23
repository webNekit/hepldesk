<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление пользователями</h2>
        <button wire:click="openModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ
        </button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ $editingUserId ? 'Редактировать пользователя' : 'Новый пользователь' }}</h3>
                <form wire:submit.prevent="save" class="space-y-4">
                    <input type="text" wire:model.live="name" placeholder="ФИО" class="w-full rounded-lg border-slate-300">
                    <input type="email" wire:model.live="email" placeholder="Email" class="w-full rounded-lg border-slate-300">
                    @if(!$editingUserId) <input type="password" wire:model.live="password" placeholder="Пароль" class="w-full rounded-lg border-slate-300"> @endif
                    
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Роли</label>
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}"> 
                                {{ $this->getRoleDisplayName($role->name) }}
                            </label>
                        @endforeach
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="bg-slate-100 text-slate-500 font-bold py-2 px-4 rounded-lg">Отмена</button>
                        <button type="submit" class="bg-emerald-900 text-white font-bold py-2 px-4 rounded-lg">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ФИО</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Роли</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">{{ $user->name }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded-full mr-1">
                                    {{ $this->getRoleDisplayName($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $user->id }})" class="text-blue-600 font-bold text-xs uppercase">Изменить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>