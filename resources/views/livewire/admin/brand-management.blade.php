<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление брендами</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingBrandId ? 'Редактировать' : 'Новый' }} бренд</h3>
            <form wire:submit.prevent="{{ $editingBrandId ? 'updateBrand' : 'createBrand' }}" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Название</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Slug</label>
                    <input type="text" wire:model="slug" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('slug') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Описание</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-300 focus:border-emerald-500"></textarea>
                    @error('description') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                        {{ $editingBrandId ? 'ОБНОВИТЬ' : 'СОЗДАТЬ' }}
                    </button>
                    @if($editingBrandId)
                        <button type="button" wire:click="reset(['name', 'slug', 'description', 'editingBrandId'])" class="bg-slate-100 text-slate-500 font-bold px-4 rounded-lg">Отмена</button>
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Бренд</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Запчастей</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Действие</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($brands as $brand)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-emerald-900">{{ $brand->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $brand->slug }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $brand->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $brand->parts_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="editBrand({{ $brand->id }})" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase">Изменить</button>
                                    <button wire:click="deleteBrand({{ $brand->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Удалить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>