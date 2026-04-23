<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление персоналом</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingUserId ? 'Редактировать' : 'Новый' }} сотрудник</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">ФИО</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Email</label>
                    <input type="email" wire:model="email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('email') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Пароль {{ $editingUserId ? '(оставьте пустым)' : '' }}</label>
                    <input type="password" wire:model="password" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('password') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Отдел</label>
                    <select wire:model="department_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                        <option value="">Выберите отдел...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Роли</label>
                    <div class="space-y-1 mt-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('selected_roles') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                        {{ $editingUserId ? 'ОБНОВИТЬ' : 'СОЗДАТЬ' }}
                    </button>
                    @if($editingUserId)
                        <button type="button" wire:click="$set('editingUserId', null)" class="bg-slate-100 text-slate-500 font-bold px-4 rounded-lg">Х</button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Таблица -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Сотрудник</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Роли</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Отдел</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Действие</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-emerald-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($u->roles as $r)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase">{{ $r->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ optional($u->department)->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $u->id }})" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase">Изменить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
