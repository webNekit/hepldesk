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
        </div>

        {{-- ПРАВАЯ ЧАСТЬ --}}
        <div class="space-y-6">

            {{-- ИСПОЛНИТЕЛЬ --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-sm text-slate-400 uppercase mb-2">Исполнитель</h3>

                @if($ticket->assignee)
                    <div class="font-bold">{{ $ticket->assignee->name }}</div>
                @else
                    <div class="text-sm text-slate-400">Не назначен</div>
                @endif
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

        </div>
    </div>
</div>