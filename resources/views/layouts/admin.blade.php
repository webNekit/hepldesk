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
        <aside class="w-64 bg-emerald-950 text-white flex-shrink-0 flex flex-col shadow-xl">
            <div class="h-20 flex items-center px-6 border-b border-emerald-900">
                <span class="text-xl font-bold tracking-wider">ГКУ ВО "МАЦ"</span>
            </div>
            
            <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
                <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-900 rounded-lg transition-colors mb-6">
                    <span class="material-symbols-outlined">arrow_back</span> На сайт
                </a>

                <div class="text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Основное</div>
                
                @role('admin|it_support')
                <a href="/it-dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('it-dashboard*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">dashboard</span> Дашборд заявок
                </a>
                @endrole

                @role('admin|manager')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Администрирование</div>
                <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/users*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">group</span> Пользователи
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/categories*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">category</span> Категории
                </a>
                @endrole

                @role('admin|it_support')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Справочник</div>
                <a href="/manage-instructions" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('manage-instructions*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">menu_book</span> Инструкции (клиенты)
                </a>
                <a href="/admin/pdf-instructions" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/pdf-instructions*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">picture_as_pdf</span> PDF (сотрудники)
                </a>
                @endrole

                @role('admin|manager')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Контент</div>
                <a href="/admin/employees" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/employees*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">badge</span> Сотрудники
                </a>
                <a href="/admin/directory" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/directory*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">contact_phone</span> Отделы
                </a>
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Склад</div>
                <a href="/admin/brands" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/brands*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">inventory</span> Бренды
                </a>
                <a href="/admin/parts" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/parts*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">build</span> Запчасти
                </a>
                @endrole
            </nav>
        </aside>

        <div class="flex-grow flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
                <h1 class="text-xl font-bold text-slate-800">@yield('header_title', 'Панель управления')</h1>
                <form method="POST" action="/logout">@csrf <button class="text-slate-500">Выход</button></form>
            </header>
            <main class="flex-grow overflow-y-auto p-8">
                {{ $slot ?? '' }} @yield('content')
            </main>
        </div>
    </div>
</body>
</html>