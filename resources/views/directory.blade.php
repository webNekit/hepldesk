@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-10">
            <h2 class="text-4xl font-extrabold text-emerald-900 uppercase tracking-tighter">Телефонный справочник</h2>
            <p class="text-slate-500 mt-2 font-medium">Контактная информация сотрудников и подразделений комитета</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-2 overflow-hidden">
            <livewire:directory-table />
        </div>
    </div>
@endsection
