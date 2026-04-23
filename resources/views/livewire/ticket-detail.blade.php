<div class="max-w-5xl mx-auto px-6 py-12">
        <div class="mb-8">
            <a href="/it-dashboard" class="text-emerald-900 hover:text-emerald-700 flex items-center gap-2 mb-4 font-bold uppercase text-sm tracking-wider">
                <span class="material-symbols-outlined">arrow_back</span>
                Назад в дашборд
            </a>
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-emerald-900">Заявка #{{ $ticket->id }}: {{ $ticket->title }}</h2>
                <div class="flex gap-2">
                    <button wire:click="updateStatus('new')" class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'new' ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-600' }}">Новая</button>
                    <button wire:click="updateStatus('in_progress')" class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'in_progress' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' }}">В работе</button>
                    <button wire:click="updateStatus('resolved')" class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'resolved' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }}">Готово</button>
                    @if($instruction)
                        <button wire:click="toggleInstruction" class="px-4 py-2 rounded-lg text-sm font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                            Посмотреть инструкцию
                        </button>
                    @endif
                </div>
            </div>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Описание -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-xl font-bold text-emerald-700">
                        {{ substr($ticket->user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ $ticket->user->name }}</div>
                        <div class="text-sm text-slate-500">{{ $ticket->user->position }} | {{ optional($ticket->user->department)->name }}</div>
                    </div>
                    <div class="ml-auto text-sm text-slate-400">
                        {{ $ticket->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>
                <div class="prose max-w-none text-slate-700">
                    <p class="whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
                <div class="mt-8 pt-8 border-t border-slate-100 flex gap-6">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase block mb-1">Категория</span>
                        <span class="text-slate-700">{{ $ticket->category->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase block mb-1">Приоритет</span>
                        <span class="{{ $ticket->priority == 'high' ? 'text-red-600 font-bold' : 'text-slate-700' }}">
                            {{ $ticket->priority == 'high' ? 'СРОЧНЫЙ' : 'Обычный' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Инструкция для мастера -->
            @if($instruction && $showInstruction)
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-8">
                    <div class="flex items-center gap-3 mb-4 text-emerald-800">
                        <span class="material-symbols-outlined font-bold">menu_book</span>
                        <h3 class="font-bold text-lg">Справочная информация (Технику)</h3>
                    </div>
                    <h4 class="font-bold text-emerald-900 mb-4">{{ $instruction->title }}</h4>
                    <ul class="instruction-list">
                        @if(is_array($instruction->steps))
                            @foreach($instruction->steps as $step)
                                <li>{{ $step }}</li>
                            @endforeach
                        @elseif(is_string($instruction->steps))
                            @foreach(explode("\n", str_replace("\r", "", $instruction->steps)) as $line)
                                @if(trim($line))
                                    <li>{{ ltrim(trim($line), "0123456789. ") }}</li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            @endif

            <!-- Чат -->
            <div class="space-y-4">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined">chat</span>
                    Комментарии ({{ $ticket->comments->count() }})
                </h3>

                @foreach($ticket->comments as $comment)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 {{ $comment->user->hasRole('it_support|admin') ? 'border-l-4 border-l-emerald-500 ml-8' : 'mr-8' }}">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sm text-slate-800">{{ $comment->user->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="text-slate-600 text-sm">
                            {{ $comment->body }}
                        </div>
                    </div>
                @endforeach

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <form wire:submit.prevent="addComment">
                        <textarea wire:model="body" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 mb-4" placeholder="Написать ответ..."></textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-emerald-900 text-white font-bold px-6 py-2 rounded-lg hover:bg-emerald-800 transition-all">
                                ОТПРАВИТЬ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Информация об исполнителе -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">Назначено на</h3>
                @if($ticket->assignee)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700">
                            {{ substr($ticket->assignee->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">{{ $ticket->assignee->name }}</div>
                            <div class="text-xs text-slate-500">ИТ-Специалист</div>
                        </div>
                    </div>
                @else
                    <div class="text-sm text-slate-500 italic">Специалист не назначен</div>
                @endif
            </div>

            <!-- Списание запчастей -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">Списание запчастей</h3>
                
                @if (session()->has('error'))
                    <div class="mb-4 text-xs text-red-600 bg-red-50 p-2 rounded">{{ session('error') }}</div>
                @endif

                <form wire:submit.prevent="attachPart" class="space-y-4">
                    <select wire:model="part_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500">
                        <option value="">Выберите запчасть...</option>
                        @foreach($parts as $part)
                            <option value="{{ $part->id }}" {{ $part->quantity <= 0 ? 'disabled' : '' }}>
                                {{ $part->name }} (Остаток: {{ $part->quantity > 0 ? $part->quantity : 'Закончилось' }})
                            </option>
                        @endforeach
                    </select>
                    
                    @if($parts->isEmpty())
                        <p class="text-xs text-slate-400 italic">Для данной категории запчасти не найдены.</p>
                    @else
                        <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-2 rounded-lg text-sm hover:bg-emerald-800 transition-all">
                            Списать запчасть
                        </button>
                    @endif
                </form>
            </div>

            <!-- История -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">История</h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5"></div>
                        <div class="text-xs">
                            <div class="text-slate-400">{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                            <div class="text-slate-700">Заявка создана пользователем {{ $ticket->user->name }}</div>
                        </div>
                    </div>
                    @if($ticket->status != 'new')
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                            <div class="text-xs">
                                <div class="text-slate-400">{{ $ticket->updated_at->format('d.m.Y H:i') }}</div>
                                <div class="text-slate-700">Статус изменен на "{{ $ticket->status }}"</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>