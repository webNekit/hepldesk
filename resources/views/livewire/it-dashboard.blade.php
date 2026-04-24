<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-emerald-900 uppercase tracking-wide">Кабинет ИТ-специалиста</h2>
            <p class="text-slate-600">Управление заявками и техническая поддержка сотрудников.</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-4 items-center">
            @role('admin')
                <select wire:model.live="filterCategory" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 py-2">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterAssignee" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 py-2">
                    <option value="">Все сотрудники</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
            @endrole

            <div class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm shadow-sm flex items-center">
                <span class="text-slate-500 mr-2">Всего заявок:</span>
                <span class="font-bold text-emerald-900">{{ \App\Models\Ticket::count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Новые -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">Новые ({{ $newTickets->count() }})</h3>
            </div>
            
            <div class="space-y-4">
                @foreach($newTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                            @if($ticket->priority == 'high')
                                <span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase">Срочно</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700">{{ $ticket->title }}</h4>
                        <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $ticket->description }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- В работе -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">В работе ({{ $inProgressTickets->count() }})</h3>
            </div>

            <div class="space-y-4">
                @foreach($inProgressTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                            @if($ticket->priority == 'high')
                                <span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase">Срочно</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700">{{ $ticket->title }}</h4>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center text-[10px] font-bold text-amber-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Готово -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">Готово ({{ $resolvedTickets->count() }})</h3>
            </div>

            <div class="space-y-4 opacity-75">
                @foreach($resolvedTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700 line-through">{{ $ticket->title }}</h4>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
