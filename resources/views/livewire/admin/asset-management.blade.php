<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Учет оборудования</h2>
            <p class="text-slate-500 font-medium">Управление инвентарем и техникой организации.</p>
        </div>
        
        <div class="flex gap-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Поиск по названию или S/N..." 
                       class="pl-10 pr-4 py-3 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 w-64 bg-white shadow-sm text-sm">
                <span class="material-symbols-outlined absolute left-3 top-3.5 text-slate-400 text-lg">search</span>
            </div>
            
            <button wire:click="openAssetModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                НОВОЕ ОБОРУДОВАНИЕ
            </button>
        </div>
    </div>

    @if($showAssetModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-md bg-black/40 animate-in fade-in duration-300">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border border-slate-200">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-emerald-900 uppercase tracking-tighter">
                        {{ $editingAssetId ? 'Редактировать оборудование' : 'Новое оборудование' }}
                    </h3>
                    <button wire:click="closeAssetModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form wire:submit.prevent="save" class="p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Название устройства</label>
                            <input type="text" wire:model="name" placeholder="Напр. HP LaserJet Pro M404n" 
                                   class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                            @error('name') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Серийный номер (S/N)</label>
                            <input type="text" wire:model="serial_number" placeholder="CNB1J23456" 
                                   class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                            @error('serial_number') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Тип устройства</label>
                            <select wire:model="type" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Выберите тип</option>
                                <option value="pc">Компьютер / Ноутбук</option>
                                <option value="printer">Принтер / МФУ</option>
                                <option value="monitor">Монитор</option>
                                <option value="network">Сетевое оборудование</option>
                                <option value="other">Другое</option>
                            </select>
                            @error('type') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Статус</label>
                            <select wire:model="status" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50 font-bold">
                                <option value="active">🟢 В работе</option>
                                <option value="repair">🟡 В ремонте</option>
                                <option value="retired">🔴 Списано</option>
                            </select>
                            @error('status') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Отдел</label>
                            <select wire:model="department_id" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Все отделы</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Ответственное лицо</label>
                            <select wire:model="user_id" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Не назначено</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeAssetModal" 
                                class="bg-slate-100 text-slate-500 font-bold py-3 px-6 rounded-xl hover:bg-slate-200 transition-all uppercase text-xs tracking-widest">
                            Отмена
                        </button>
                        <button type="submit" 
                                class="bg-emerald-900 text-white font-bold py-3 px-8 rounded-xl hover:bg-emerald-800 transition-all uppercase text-xs tracking-widest shadow-lg">
                            {{ $editingAssetId ? 'Обновить' : 'Сохранить' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Оборудование</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Тип</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Владелец / Отдел</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Статус</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900 group-hover:text-emerald-900 transition-colors">{{ $asset->name }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-1">S/N: {{ $asset->serial_number ?? '—' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                    {{ $asset->type }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-sm">
                                <div class="font-bold text-slate-700">{{ optional($asset->user)->name ?? '—' }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-medium">{{ optional($asset->department)->name ?? 'Без отдела' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'repair' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'retired' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusNames = [
                                        'active' => 'В работе',
                                        'repair' => 'В ремонте',
                                        'retired' => 'Списано',
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-[10px] font-extrabold uppercase border {{ $statusClasses[$asset->status] ?? 'bg-slate-100' }}">
                                    {{ $statusNames[$asset->status] ?? $asset->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right space-x-3">
                                <button wire:click="edit({{ $asset->id }})" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                    <span class="material-symbols-outlined text-lg">edit_note</span>
                                </button>
                                <button wire:click="delete({{ $asset->id }})" wire:confirm="Вы уверены?" class="text-red-400 hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-medium">
                                <span class="material-symbols-outlined text-4xl mb-3 block">inventory_2</span>
                                Оборудование не найдено.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
            {{ $assets->links() }}
        </div>
    </div>
</div>
