<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'ГКУ ВО "МАЦ"' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background text-on-background font-body-lg antialiased">
        <nav class="bg-emerald-950 fixed top-0 w-full z-50 h-20 border-b border-emerald-900 font-['Public_Sans'] shadow-lg">
            <div class="flex justify-between items-center max-w-7xl mx-auto px-6 lg:px-24 w-full h-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-900 font-bold">agriculture</span>
                    </div>
                    <div class="text-xl font-extrabold text-white uppercase tracking-tighter">
                        ГКУ ВО "МАЦ"
                    </div>
                </div>
                <div class="hidden md:flex space-x-2 items-center">
                     <a href="/" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('/') ? 'bg-emerald-900 text-white font-bold' : '' }}">Главная</a>
                     <a href="/directory" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('directory') ? 'bg-emerald-900 text-white font-bold' : '' }}">Справочник</a>
                     <a href="/support" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('support') ? 'bg-emerald-900 text-white font-bold' : '' }}">Поддержка</a>
                 </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center gap-4">
                            @hasanyrole('admin|it_support|manager')
                                <a href="/it-dashboard" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-bold border border-white/20 transition-all">Панель управления</a>
                            @endhasanyrole
                            <div class="flex flex-col items-end">
                                <span class="text-sm text-white font-bold">{{ auth()->user()->name }}</span>
                                <form method="POST" action="/logout" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-emerald-300 hover:text-white uppercase font-bold tracking-widest transition-colors">Выход</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed px-6 py-2 rounded-lg font-bold transition-all shadow-md active:scale-95">Вход</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="pt-20 min-h-screen">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <footer class="bg-slate-900 text-white w-full py-16 mt-20 border-t border-emerald-900 font-['Public_Sans']">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 max-w-7xl mx-auto px-6 lg:px-24">
                <div class="md:col-span-4">
                    <div class="text-lg font-bold text-emerald-400 mb-4">ГКУ ВО "МАЦ"</div>
                    <p class="text-slate-400 mb-6 max-w-xs text-sm">
                        Институциональная стабильность и современный прогресс. Обеспечение роста и безопасности сельскохозяйственного сектора Волгоградской области.
                    </p>
                    <div class="text-slate-500 text-xs uppercase font-bold tracking-widest">
                        © 2024 Все права защищены.
                    </div>
                </div>
                <div class="md:col-span-8 flex flex-wrap gap-8 justify-end">
                    <div class="flex flex-col gap-3">
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Противодействие коррупции</a>
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Открытые данные</a>
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Политика конфиденциальности</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
