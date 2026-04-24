<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-extrabold text-emerald-900 uppercase tracking-tighter mb-4">База знаний</h2>
        <p class="text-slate-500 max-w-2xl mx-auto">Инструкции и руководства по решению типичных технических проблем.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-6 mb-12">
        <div class="flex-1 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Поиск по названию инструкции..." 
                   class="w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white">
            <span class="material-symbols-outlined absolute left-4 top-4.5 text-slate-400">search</span>
        </div>
        
        <select wire:model.live="selectedCategory" class="md:w-64 py-4 rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white">
            <option value="">Все категории</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($instructions as $instruction)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden hover:shadow-2xl transition-all duration-300 group flex flex-col">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                            {{ $instruction->category->name }}
                        </span>
                        @if($instruction->pdf_path)
                            <span class="material-symbols-outlined text-red-500" title="Доступен PDF">picture_as_pdf</span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 mb-4 group-hover:text-emerald-800 transition-colors">
                        {{ $instruction->title }}
                    </h3>

                    @if($instruction->steps)
                        <div class="space-y-3">
                            @foreach(array_slice($instruction->steps, 0, 3) as $step)
                                <div class="flex gap-3 items-start">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 text-[10px] flex items-center justify-center flex-shrink-0 font-bold mt-0.5">{{ $loop->iteration }}</span>
                                    <p class="text-sm text-slate-600 line-clamp-1">{{ $step }}</p>
                                </div>
                            @endforeach
                            @if(count($instruction->steps) > 3)
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest pl-8">И еще {{ count($instruction->steps) - 3 }} шагов...</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-2">
                    @if($instruction->pdf_path)
                        <a href="{{ asset('storage/' . $instruction->pdf_path) }}" target="_blank" 
                           class="flex-1 bg-white border border-slate-200 text-slate-700 font-bold py-3 rounded-xl text-xs text-center uppercase tracking-wider hover:bg-slate-100 transition-colors">
                            Открыть PDF
                        </a>
                    @endif
                    <button class="flex-1 bg-emerald-900 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                        Подробнее
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center text-slate-400 font-medium">
                <span class="material-symbols-outlined text-6xl mb-4 block">find_in_page</span>
                Ничего не найдено по вашему запросу.
            </div>
        @endforelse
    </div>
</div>
