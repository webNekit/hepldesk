<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-white hover:bg-white/10 rounded-lg transition-colors flex items-center">
        <span class="material-symbols-outlined">notifications</span>
        @if($notifications->count() > 0)
            <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-emerald-950">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" 
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-[100]"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         style="display: none;">
        
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest">Уведомления</h3>
            @if($notifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-emerald-600 font-bold hover:underline">Прочитать все</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors group">
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $notification->data['message'] }}</p>
                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-slate-300 hover:text-emerald-500 transition-colors">
                            <span class="material-symbols-outlined text-sm">done_all</span>
                        </button>
                    </div>
                    @php
                        $route = '#';
                        try {
                            if (isset($notification->data['ticket_id'])) {
                                if (auth()->user()->hasRole(['admin', 'it_support'])) {
                                    $route = route('ticket-detail', $notification->data['ticket_id']);
                                } else {
                                    // For regular users, we'd need a tracking link or similar
                                    // For now, let's just not show the link if they don't have access
                                }
                            }
                        } catch (\Exception $e) {}
                    @endphp
                    @if($route !== '#')
                        <a href="{{ $route }}" class="mt-2 inline-block text-[10px] font-bold text-emerald-600 uppercase hover:underline">Перейти к заявке</a>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs italic">
                    Нет новых уведомлений
                </div>
            @endforelse
        </div>
    </div>
</div>
