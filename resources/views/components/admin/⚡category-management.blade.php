<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление категориями</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg shadow-sm border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingCategoryId ? 'Редактировать' : 'Добавить' }} категорию</h3>
            <form wire:submit.prevent="createCategory" @submit.prevent="editingCategoryId ? updateCategory() : createCategory()" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Название категории</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Напр: Замена картриджа">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all shadow-md active:scale-95">
                        {{ $editingCategoryId ? 'ОБНОВИТЬ' : 'СОЗДАТЬ' }}
                    </button>
                    @if($editingCategoryId)
                        <button type="button" wire:click="$set('editingCategoryId', null)" class="bg-slate-100 text-slate-500 font-bold px-6 rounded-lg hover:bg-slate-200 transition-colors">ОТМЕНА</button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Список категорий -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($categories as $category)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group">
                    <div class="p-6 flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg">{{ $category->name }}</h4>
                            <div class="mt-4 space-y-2 text-sm text-slate-600">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                    <span>Запчастей: {{ $category->parts_count }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">menu_book</span>
                                    <span>Инструкций: {{ $category->instructions_count }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">support_agent</span>
                                    <span>Заявок: {{ $category->tickets_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="editCategory({{ $category->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button onclick="return confirm('Удалить эту категорию? Это также удалит все связанные запчасти, инструкции и заявки. Продолжить?')" wire:click="deleteCategory({{ $category->id }})" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div>
    {{-- We must ship. - Taylor Otwell --}}
</div>