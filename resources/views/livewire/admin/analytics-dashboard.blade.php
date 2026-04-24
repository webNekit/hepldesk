<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Аналитика и отчетность</h2>
        <p class="text-slate-500 font-medium">Обзор эффективности работы службы технической поддержки.</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">confirmation_number</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Всего заявок</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Решено</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['resolved'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">pending_actions</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">В ожидании</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['pending'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">star</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Средняя оценка</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['avg_rating'] ?: '—' }} <span class="text-xs text-slate-300 font-medium">/ 5</span></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- PERFORMANCE BY TECH --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-extrabold text-emerald-900 uppercase tracking-tighter">Производительность специалистов</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Топ-5 по решенным заявкам</span>
            </div>
            <div class="p-8 space-y-6">
                @foreach($techPerformance as $tech)
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-900 text-white flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr($tech->name, 0, 2) }}
                                </div>
                                <div class="font-bold text-slate-800 text-sm">{{ $tech->name }}</div>
                            </div>
                            <div class="text-sm font-black text-emerald-600">{{ $tech->resolved_count }} заявок</div>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" 
                                 style="width: {{ $stats['resolved'] > 0 ? ($tech->resolved_count / $stats['resolved'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- STATUS DISTRIBUTION --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-extrabold text-emerald-900 uppercase tracking-tighter text-center">Распределение по статусам</h3>
            </div>
            <div class="p-8 flex-grow flex flex-col justify-center space-y-4">
                @foreach($statusDistribution as $dist)
                    @php
                        $color = match($dist->status) {
                            'new' => 'blue',
                            'in_progress' => 'amber',
                            'resolved' => 'emerald',
                            default => 'slate'
                        };
                        $label = match($dist->status) {
                            'new' => 'Новые',
                            'in_progress' => 'В работе',
                            'resolved' => 'Решено',
                            default => $dist->status
                        };
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-100 group hover:scale-[1.02] transition-transform">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-{{ $color }}-500"></div>
                            <span class="text-sm font-bold text-{{ $color }}-900 uppercase tracking-wide">{{ $label }}</span>
                        </div>
                        <span class="text-lg font-black text-{{ $color }}-700">{{ $dist->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
