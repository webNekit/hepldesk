<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление инструкциями</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg shadow-sm border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Форма-репитер -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingInstructionId ? 'Редактировать' : 'Добавить' }} инструкцию</h3>
            <form wire:submit.prevent="save" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Категория</label>
                    <select wire:model="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Выберите категорию...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Заголовок</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Напр: Как сбросить пароль">
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Шаги инструкции</label>
                    @foreach($steps as $index => $step)
                        <div class="flex gap-2">
                            <div class="flex-grow">
                                <input type="text" wire:model="steps.{{ $index }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500" placeholder="Шаг {{ $index + 1 }}">
                                @error('steps.'.$index) <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <button type="button" wire:click="removeStep({{ $index }})" class="text-slate-400 hover:text-red-600 p-2">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addStep" class="flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-sm">add_circle</span>
                        Добавить шаг
                    </button>
                    @error('steps') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all shadow-md active:scale-95">
                        {{ $editingInstructionId ? 'ОБНОВИТЬ' : 'СОХРАНИТЬ' }}
                    </button>
                    @if($editingInstructionId)
                        <button type="button" wire:click="$set('editingInstructionId', null)" class="bg-slate-100 text-slate-500 font-bold px-6 rounded-lg hover:bg-slate-200 transition-colors">ОТМЕНА</button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Список инструкций -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($instructions as $inst)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group">
                    <div class="p-6 flex justify-between items-start">
                        <div>
                            <span class="inline-block px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase rounded mb-2">{{ $inst->category->name }}</span>
                            <h4 class="font-bold text-slate-800 text-lg">{{ $inst->title }}</h4>
                            <div class="mt-4 space-y-2">
                                @foreach($inst->steps as $step)
                                    <div class="flex items-center gap-3 text-sm text-slate-500">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold">{{ $loop->iteration }}</span>
                                        {{ $step }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $inst->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button onclick="confirm('Удалить эту инструкцию?') || event.stopImmediatePropagation()" wire:click="delete({{ $inst->id }})" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
