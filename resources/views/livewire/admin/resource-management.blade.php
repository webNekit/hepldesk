<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Государственные ресурсы</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingResourceId ? 'Редактировать' : 'Добавить' }} ресурс</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Название</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Ссылка (URL)</label>
                    <input type="url" wire:model="url" class="w-full rounded-lg border-slate-300 focus:border-emerald-500" placeholder="https://...">
                    @error('url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all">
                    {{ $editingResourceId ? 'ОБНОВИТЬ' : 'СОХРАНИТЬ' }}
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Ресурс</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($resources as $res)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-emerald-900">{{ $res->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $res->url }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $res->id }})" class="text-blue-600 hover:text-blue-900 mr-4 font-bold text-xs uppercase">Изменить</button>
                                    <button onclick="confirm('Удалить?') || event.stopImmediatePropagation()" wire:click="delete({{ $res->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Удалить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
