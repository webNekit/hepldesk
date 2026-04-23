<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-['Public_Sans'] antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-emerald-950 text-white flex-shrink-0 flex flex-col shadow-xl">
            <div class="h-20 flex items-center px-6 border-b border-emerald-900">
                <span class="text-xl font-bold tracking-wider">ГКУ ВО "МАЦ"</span>
            </div>
            
            <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
                <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-900 rounded-lg transition-colors mb-6">
                    <span class="material-symbols-outlined">arrow_back</span>
                    На сайт
                </a>

                <div class="text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Основное</div>
                
                @role('admin|it_support')
                <a href="/it-dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('it-dashboard*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Дашборд заявок
                </a>
                @endrole

                @role('admin')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Администрирование</div>
                <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/users*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">group</span>
                    Пользователи
                </a>
                @endrole

                @role('admin|it_support')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Поддержка</div>
                <a href="/manage-instructions" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('manage-instructions*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">menu_book</span>
                    Инструкции
                </a>
                @endrole

                @role('admin|manager')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Контент</div>
                <a href="/admin/directory" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/directory*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">contact_phone</span>
                    Справочник
                </a>
                <a href="/admin/resources" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/resources*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">link</span>
                    Гос. ресурсы
                </a>
                @endrole
            </nav>

            <div class="p-4 border-t border-emerald-900">
                <div class="flex items-center gap-3 px-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-800 flex items-center justify-center font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-emerald-400 uppercase font-bold">{{ auth()->user()->roles->first()->name }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm z-10">
                <h1 class="text-xl font-bold text-slate-800">
                    @yield('header_title', 'Панель управления')
                </h1>
                
                <div class="flex items-center gap-4">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-slate-500 hover:text-red-600 transition-colors font-semibold">
                            <span class="material-symbols-outlined">logout</span>
                            Выход
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-grow overflow-y-auto p-8">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
    </div>
</body>
</html>
