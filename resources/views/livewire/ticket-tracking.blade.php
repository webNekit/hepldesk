<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Отслеживание заявки #{{ $ticket->id }}</h2>
        <p class="text-slate-500 font-medium mt-1">Создана {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-slate-800">{{ $ticket->title }}</h3>
                <p class="text-slate-500 mt-1">{{ optional($ticket->category)->name }}</p>
            </div>
            <div>
                @php
                    $statusColors = [
                        'new' => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-amber-100 text-amber-800',
                        'resolved' => 'bg-emerald-100 text-emerald-800',
                    ];
                    $statusNames = [
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'resolved' => 'Решена',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full font-bold text-sm uppercase {{ $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-800' }}">
                    {{ $statusNames[$ticket->status] ?? $ticket->status }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <h4 class="text-sm font-bold text-slate-500 uppercase mb-2">Описание проблемы</h4>
            <p class="text-slate-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
            
            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                <div>
                    <span class="block text-xs font-bold text-slate-500 uppercase">Контактное лицо</span>
                    <span class="font-medium text-slate-800">{{ $ticket->contact_name }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-500 uppercase">Ответственный специалист</span>
                    <span class="font-medium text-slate-800">{{ optional($ticket->assignee)->name ?? 'Не назначен' }}</span>
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-2xl font-bold text-slate-800 uppercase tracking-tighter mb-6">История изменений</h3>
    
    <div class="relative border-l-2 border-emerald-200 ml-3 space-y-8 pb-8">
        @forelse($ticket->activities as $activity)
            <div class="relative pl-8">
                <!-- Timeline dot -->
                <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-emerald-500 ring-4 ring-white"></div>
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                    <p class="text-sm text-slate-500 mb-1">{{ $activity->created_at->format('d.m.Y H:i') }} &mdash; <span class="font-bold">{{ optional($activity->causer)->name ?? 'Система' }}</span></p>
                    
                    <p class="text-slate-800">
                        @if($activity->event === 'created')
                            Заявка успешно зарегистрирована в системе.
                        @elseif($activity->event === 'updated')
                            @php
                                $changes = $activity->properties->toArray();
                            @endphp
                            @if(isset($changes['attributes']['status']))
                                Статус изменен на <strong>{{ $statusNames[$changes['attributes']['status']] ?? $changes['attributes']['status'] }}</strong>
                            @elseif(isset($changes['attributes']['assigned_to']))
                                Назначен специалист ИТ-отдела.
                            @else
                                Заявка обновлена.
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="pl-8 text-slate-500 italic">Событий пока нет.</div>
        @endforelse
    </div>

    {{-- CSAT (Оценка качества) --}}
    @if($ticket->status == 'resolved')
        <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-200 mt-12 shadow-sm">
            <h3 class="font-extrabold text-2xl text-emerald-900 mb-2 uppercase tracking-tighter">Оцените качество обслуживания</h3>
            <p class="text-emerald-700 mb-6">Ваша заявка решена! Пожалуйста, оцените работу нашего специалиста.</p>
            
            @if(session()->has('message'))
                <div class="bg-emerald-600 text-white p-4 rounded-lg mb-6 font-bold">
                    {{ session('message') }}
                </div>
            @endif

            @if(!$ticket->rating)
                <form wire:submit.prevent="submitFeedback">
                    <div class="flex items-center gap-6 mb-6">
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="1" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">😡</div>
                            <span class="text-xs font-bold text-emerald-800">1</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="2" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🙁</div>
                            <span class="text-xs font-bold text-emerald-800">2</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="3" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">😐</div>
                            <span class="text-xs font-bold text-emerald-800">3</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="4" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🙂</div>
                            <span class="text-xs font-bold text-emerald-800">4</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="5" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🤩</div>
                            <span class="text-xs font-bold text-emerald-800">5</span>
                        </label>
                    </div>
                    @error('rating') <span class="text-red-500 text-sm block mb-4">{{ $message }}</span> @enderror
                    
                    <textarea wire:model="feedback_comment" class="w-full rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 mb-4 bg-white" rows="3" placeholder="Оставьте комментарий (необязательно)"></textarea>
                    
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-wider transition-colors shadow-lg">
                        Отправить оценку
                    </button>
                </form>
            @else
                <div class="bg-white p-6 rounded-xl border border-emerald-100">
                    <div class="text-emerald-900 mb-2">
                        Ваша оценка: <strong class="text-xl">{{ $ticket->rating }} ⭐</strong>
                    </div>
                    @if($ticket->feedback_comment)
                        <div class="text-slate-600 bg-slate-50 p-4 rounded-lg italic border border-slate-100">
                            &laquo;{{ $ticket->feedback_comment }}&raquo;
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
