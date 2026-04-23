@extends('layouts.app')

@section('content')
    <section class="relative w-full min-h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACKqpnlTPoNj3U6UR33X72XE9euFYb4CenyvHWb-FRgs41Zo1I_7epi96KZmHhoL33QnSU2v7Po0iaWO0Nn7ckGPA6-cnmHpEFFJkc_p8dCwYgcIUgdxZ9XLifAealYwFSs0D9ueSzjlYxkEQ45xh7He0WMuGOk33oGB2ClsZzV5hGjlzZWaPpuelt68NKl2Z66KTJ-dhERLWBWC7352rpVqG00uVah2SDWzY8196fEhjha2BcqSjC57NHpHmPTq0fBJVixdOfUx5D" />
            <div class="absolute inset-0 bg-emerald-950/60 mix-blend-multiply"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 drop-shadow-lg leading-tight">
                Комитет сельского хозяйства <br> <span class="text-white font-medium text-3xl md:text-5xl">Волгоградской области</span>
            </h1>
            <p class="text-xl text-white mb-10 max-w-3xl mx-auto font-medium opacity-90">
                Корпоративный портал для сотрудников. Единый справочник и система подачи заявок в службу технической поддержки.
            </p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="/support" class="bg-emerald-500 text-white hover:bg-emerald-400 px-10 py-5 rounded-xl font-bold transition-all shadow-xl active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined">support_agent</span>
                    Подать заявку
                </a>
                <a href="/directory" class="bg-white/10 backdrop-blur-md text-white border border-white/30 hover:bg-white/20 px-10 py-5 rounded-xl font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">contact_phone</span>
                    Справочник
                </a>
            </div>
        </div>
    </section>

    <!-- Гос ресурсы -->
    <section class="bg-white border-y border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-3 mb-8">
                <span class="material-symbols-outlined text-emerald-700">account_balance</span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Государственные ресурсы</h2>
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($resources as $res)
                    <a href="{{ $res->url }}" target="_blank" class="px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-emerald-50/10 hover:border-emerald-500 transition-all font-bold text-slate-600 flex items-center gap-2 group">
                        {{ $res->name }}
                        <span class="material-symbols-outlined text-sm opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">hub</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Техподдержка</h3>
                <p class="text-slate-500 leading-relaxed">Быстрое решение технических проблем и консультации специалистов ИТ-отдела.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group border-b-4 border-b-emerald-500">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">style</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Справочник</h3>
                <p class="text-slate-500 leading-relaxed">Контакты всех сотрудников и отделов комитета. Удобный поиск по должностям и кабинетам.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">menu_book</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">База знаний</h3>
                <p class="text-slate-500 leading-relaxed">Инструкции и руководства для самостоятельного решения типовых задач без ожидания мастера.</p>
            </div>
        </div>
    </section>
@endsection
