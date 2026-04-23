<div class="max-w-4xl mx-auto py-12 px-6">
    <h2 class="text-3xl font-bold mb-8 text-slate-800">Создать заявку</h2>

    <form wire:submit.prevent="save" class="space-y-6 bg-white p-8 rounded-xl shadow-sm border border-slate-200">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="text" wire:model="contact_name" placeholder="ФИО"
                class="w-full rounded-xl border-slate-300 focus:border-emerald-500">

            <input type="text" wire:model="contact_phone" placeholder="Телефон"
                class="w-full rounded-xl border-slate-300 focus:border-emerald-500">

            <input type="email" wire:model="contact_email" placeholder="Email"
                class="w-full rounded-xl border-slate-300 focus:border-emerald-500">

            <select wire:model.live="category_id" class="w-full rounded-xl border-slate-300 focus:border-emerald-500">
                <option value="">Выберите категорию</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="text" wire:model="title" placeholder="Тема заявки"
            class="w-full rounded-xl border-slate-300 focus:border-emerald-500">

        <textarea wire:model="description" placeholder="Описание проблемы" rows="4"
            class="w-full rounded-xl border-slate-300 focus:border-emerald-500"></textarea>

        {{-- ИНСТРУКЦИИ --}}
        @if(count($instructions))
            <div class="space-y-4">
                @foreach($instructions as $instruction)
                    <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <h4 class="font-bold text-emerald-900 mb-3">
                            {{ $instruction->title }}
                        </h4>

                        <ul class="space-y-1 text-sm text-slate-700">
                            @foreach($instruction->steps as $step)
                                @if(trim($step))
                                    <li class="flex gap-2">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>{{ $step }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        <button type="submit"
            class="w-full bg-emerald-900 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-lg">
            ОТПРАВИТЬ ЗАЯВКУ
        </button>
    </form>
</div>