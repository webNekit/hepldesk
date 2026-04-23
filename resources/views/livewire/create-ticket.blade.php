<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-emerald-900 uppercase tracking-wide">Техническая поддержка</h2>
        <p class="text-slate-600">Пожалуйста, выберите категорию вашей проблемы и заполните форму.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <form wire:submit.prevent="save">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase tracking-wide">Категория заявки</label>
                    <select wire:model.live="category_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Выберите категорию...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase tracking-wide">Приоритет</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="priority" value="normal" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2">Обычный</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="priority" value="high" class="text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-red-600 font-semibold">Срочный</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase tracking-wide">Тема сообщения</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Краткое описание проблемы">
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase tracking-wide">Подробное описание</label>
                    <textarea wire:model="description" rows="5" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Опишите проблему как можно подробнее..."></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-4 rounded-lg hover:bg-emerald-800 transition-all shadow-md active:scale-95">
                    ОТПРАВИТЬ ЗАЯВКУ
                </button>
            </form>
        </div>

        <!-- Инструкции -->
        <div>
            @if($instruction)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 mb-6 animate-fade-in">
                    <div class="flex items-center gap-3 mb-4 text-amber-800">
                        <span class="material-symbols-outlined font-bold">lightbulb</span>
                        <h3 class="font-bold text-lg">Рекомендуемая инструкция</h3>
                    </div>
                    <h4 class="font-bold text-emerald-900 mb-4">{{ $instruction->title }}</h4>
                    <ul class="instruction-list">
                        @foreach(explode("\n", str_replace("\r", "", $instruction->content)) as $line)
                            @if(trim($line))
                                <li>{{ ltrim(trim($line), "0123456789. ") }}</li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="mt-6 pt-6 border-t border-amber-200 text-sm text-amber-800 italic">
                        Если инструкция не помогла, пожалуйста, заполните форму слева.
                    </div>
                </div>
            @else
                <div class="bg-slate-50 border border-slate-200 border-dashed rounded-xl p-12 text-center h-full flex flex-col justify-center items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-4">info</span>
                    <p class="text-slate-500">Выберите категорию, чтобы увидеть рекомендации по самостоятельному решению проблемы.</p>
                </div>
            @endif
        </div>
    </div>
</div>
