<div class="max-w-5xl mx-auto px-6 py-12">
    <div class="mb-8">
        <a href="/it-dashboard"
            class="text-emerald-900 hover:text-emerald-700 flex items-center gap-2 mb-4 font-bold uppercase text-sm tracking-wider">
            ← Назад в дашборд
        </a>

        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-emerald-900">
                Заявка #{{ $ticket->id }}: {{ $ticket->title }}
            </h2>

            <div class="flex gap-2 flex-wrap">

                <button wire:click="updateStatus('new')"
                    class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'new' ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                    Новая
                </button>

                <button wire:click="updateStatus('in_progress')"
                    class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'in_progress' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                    В работе
                </button>

                <button wire:click="updateStatus('resolved')"
                    class="px-4 py-2 rounded-lg text-sm font-bold {{ $ticket->status == 'resolved' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                    Готово
                </button>

                {{-- ТЕКСТОВАЯ ИНСТРУКЦИЯ --}}
                @if(isset($instruction) && $instruction)
                    <button wire:click="toggleInstruction"
                        class="px-4 py-2 rounded-lg text-sm font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                        Инструкция
                    </button>
                @endif

                {{-- PDF --}}
                @if(isset($pdfInstruction) && $pdfInstruction)
                    <a href="{{ asset('storage/' . $pdfInstruction->pdf_path) }}" target="_blank"
                        class="px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-800 hover:bg-blue-200">
                        PDF инструкция
                    </a>
                @endif

            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ЛЕВАЯ ЧАСТЬ --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- ОПИСАНИЕ --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                <p class="text-slate-700 whitespace-pre-line">{{ $ticket->description }}</p>
            </div>

            {{-- ИНСТРУКЦИЯ --}}
            @if(isset($instruction) && $instruction && $showInstruction)
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
                    <h4 class="font-bold text-emerald-900 mb-3">
                        {{ $instruction->title }}
                    </h4>

                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($instruction->steps ?? [] as $step)
                            @if(trim($step))
                                <li class="flex gap-2">
                                    <span class="text-emerald-600 font-bold">•</span>
                                    <span>{{ $step }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- КОММЕНТАРИИ --}}
            <div class="space-y-4">
                <h3 class="font-bold text-lg">Комментарии</h3>

                @foreach($ticket->comments as $comment)
                    <div class="bg-white p-4 rounded-xl border">
                        <div class="text-sm font-bold">{{ $comment->user->name }}</div>
                        <div class="text-sm text-slate-600">{{ $comment->body }}</div>
                    </div>
                @endforeach

                <form wire:submit.prevent="addComment" class="bg-white p-4 rounded-xl border">
                    <textarea wire:model="body" class="w-full rounded-lg border-slate-300 mb-3"
                        placeholder="Комментарий"></textarea>

                    <button type="submit" class="bg-emerald-900 text-white px-4 py-2 rounded-lg">
                        Отправить
                    </button>
                </form>
            </div>

            {{-- ОТЗЫВ ЗАКАЗЧИКА --}}
            @if($ticket->status === 'resolved')
                <div class="bg-white rounded-xl shadow-lg border-2 border-emerald-100 overflow-hidden mt-12">
                    <div class="bg-emerald-50 px-6 py-3 border-b border-emerald-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-sm">reviews</span>
                        <h3 class="font-bold text-emerald-900 text-sm uppercase tracking-wider">Отзыв заказчика</h3>
                    </div>
                    <div class="p-8">
                        @if($ticket->rating)
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined {{ $i <= $ticket->rating ? 'fill-1' : '' }}" style="font-variation-settings: 'FILL' {{ $i <= $ticket->rating ? '1' : '0' }}">star</span>
                                    @endfor
                                </div>
                                <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-black">{{ $ticket->rating }} / 5</span>
                            </div>
                            
                            @if($ticket->feedback_comment)
                                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 italic text-slate-700 relative">
                                    <span class="absolute -top-3 -left-2 text-6xl text-slate-200 font-serif leading-none">“</span>
                                    {{ $ticket->feedback_comment }}
                                    <span class="absolute -bottom-8 -right-2 text-6xl text-slate-200 font-serif leading-none rotate-180">“</span>
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col items-center justify-center py-4 text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 opacity-20">sentiment_neutral</span>
                                <p class="text-sm italic">Заказчик еще не оставил отзыв</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ПРАВАЯ ЧАСТЬ --}}
        <div class="space-y-6">

            {{-- ЗАКАЗЧИК --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 border-l-4 border-l-emerald-500">
                <h3 class="text-sm text-slate-400 uppercase mb-4">Информация о заказчике</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-slate-400 text-lg">person</span>
                        <div>
                            <div class="text-xs text-slate-400">ФИО</div>
                            <div class="font-bold text-slate-800">{{ $ticket->contact_name ?? $ticket->user->name }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-slate-400 text-lg">call</span>
                        <div>
                            <div class="text-xs text-slate-400">Телефон</div>
                            <div class="font-medium text-slate-700">{{ $ticket->contact_phone ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-slate-400 text-lg">mail</span>
                        <div>
                            <div class="text-xs text-slate-400">Email</div>
                            <a href="mailto:{{ $ticket->contact_email }}?subject=Заявка #{{ $ticket->id }}&body=Здравствуйте! Отследить статус вашей заявки можно по ссылке: {{ $ticket->uuid ? route('track', $ticket->uuid) : 'пока не сгенерирована' }}" 
                               class="font-bold text-emerald-700 hover:text-emerald-900 hover:underline transition-colors break-all">
                                {{ $ticket->contact_email ?? '—' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ИСПОЛНИТЕЛЬ --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-sm text-slate-400 uppercase mb-2">Исполнитель</h3>

                @if($ticket->assignee)
                    <div class="font-bold">{{ $ticket->assignee->name }}</div>
                @else
                    <div class="text-sm text-slate-400">Не назначен</div>
                @endif
            </div>

            {{-- ОБОРУДОВАНИЕ --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-sm text-slate-400 uppercase mb-2">Привязанное оборудование</h3>

                <div class="space-y-4">
                    <select wire:model="asset_id" wire:change="updateAsset" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">Выберите устройство</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">
                                {{ $asset->name }} ({{ $asset->serial_number }})
                            </option>
                        @endforeach
                    </select>

                    @if($ticket->asset)
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <div class="text-xs font-bold text-slate-800">{{ $ticket->asset->name }}</div>
                            <div class="text-[10px] text-slate-400">S/N: {{ $ticket->asset->serial_number }}</div>
                            <div class="mt-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-700">
                                    {{ $ticket->asset->status }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ЗАПЧАСТИ --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-sm text-slate-400 uppercase mb-2">Запчасти</h3>

                <form wire:submit.prevent="attachPart" class="space-y-3">
                    <select wire:model="part_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Выберите</option>
                        @foreach($parts as $part)
                            <option value="{{ $part->id }}">
                                {{ $part->name }} ({{ $part->quantity }})
                            </option>
                        @endforeach
                    </select>

                    <button class="w-full bg-emerald-700 text-white py-2 rounded-lg">
                        Списать
                    </button>
                </form>
            </div>

            {{-- ССЫЛКА ДЛЯ КЛИЕНТА --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-sm text-slate-400 uppercase mb-2">Публичная ссылка</h3>
                <p class="text-xs text-slate-500 mb-4">Отправьте эту ссылку клиенту, чтобы он мог следить за историей заявки и оставить оценку.</p>
                
                @if($ticket->uuid)
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ route('track', $ticket->uuid) }}" class="w-full rounded-lg border-slate-300 bg-slate-50 text-xs text-slate-500 cursor-text" id="trackingLink">
                        <button onclick="navigator.clipboard.writeText(document.getElementById('trackingLink').value); alert('Ссылка скопирована!')" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-2 rounded-lg text-xs font-bold transition-colors" title="Скопировать">
                            Скопировать
                        </button>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg">
                        <p class="text-xs text-amber-700 mb-2">У этой заявки нет публичного ключа (старая заявка).</p>
                        <button wire:click="generateUuid" class="bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-amber-700 transition-colors">
                            Сгенерировать ключ
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>