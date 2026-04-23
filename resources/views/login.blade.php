@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto px-6 py-20">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-8">
            <h2 class="text-2xl font-bold text-emerald-900 mb-6 text-center uppercase tracking-wider">Вход в систему</h2>
            
            <form method="POST" action="/login">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase">Email</label>
                    <input type="email" name="email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-200" required>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase">Пароль</label>
                    <input type="password" name="password" value="password" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-200" required>
                </div>
                
                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                    ВОЙТИ
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100">
                <p class="text-xs text-slate-400 uppercase font-bold mb-4 text-center">Тестовые аккаунты</p>
                <div class="space-y-2">
                    <div class="p-3 bg-slate-50 rounded text-xs">
                        <span class="font-bold block">Сотрудник:</span>
                        <span class="text-slate-500">ivanov@mats.ru / password</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded text-xs">
                        <span class="font-bold block">ИТ-Специалист:</span>
                        <span class="text-slate-500">admin@mats.ru / password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
