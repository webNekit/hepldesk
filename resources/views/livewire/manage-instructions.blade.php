<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">
            Инструкции для клиентов
        </h2>
        <button wire:click="openModal"
            class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            ДОБАВИТЬ ИНСТРУКЦИЮ
        </button>
    </div>

    {{-- МОДАЛКА --}}
    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-8">

                <h3 class="text-xl font-bold text-slate-800 mb-6">
                    {{ $editingId ? 'Редактировать инструкцию' : 'Новая инструкция' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-6">

                    {{-- Название --}}
                    <input type="text" wire:model="title" placeholder="Название инструкции"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-200">

                    {{-- Категория --}}
                    <select wire:model="category_id" class="w-full rounded-xl border-slate-300 focus:border-emerald-500">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>

                    {{-- Шаги --}}
                    <div class="space-y-3">
                        <div class="text-sm font-bold text-slate-500 uppercase">
                            Шаги инструкции
                        </div>

                        @foreach($steps as $index => $step)
                            <div class="flex items-center gap-3">

                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-sm font-bold">
                                    {{ $index + 1 }}
                                </div>

                                <input type="text" wire:model="steps.{{ $index }}"
                                    class="w-full rounded-xl border-slate-300 focus:border-emerald-500"
                                    placeholder="Описание шага">

                                <button type="button" wire:click="removeStep({{ $index }})"
                                    class="text-red-500 hover:text-red-700 font-bold text-sm">
                                    ✕
                                </button>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addStep"
                            class="text-emerald-700 font-bold text-sm hover:underline">
                            + Добавить шаг
                        </button>
                    </div>

                    {{-- КНОПКИ --}}
                    <div class="pt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                            class="bg-slate-100 text-slate-600 font-bold py-2 px-5 rounded-xl hover:bg-slate-200">
                            Отмена
                        </button>

                        <button type="submit"
                            class="bg-emerald-900 text-white font-bold py-2 px-5 rounded-xl hover:bg-emerald-800 transition-all">
                            Сохранить
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    {{-- СПИСОК ИНСТРУКЦИЙ --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        @forelse($instructions as $inst)
            <div class="p-5 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-bold text-emerald-900">
                            {{ $inst->title }}
                        </h4>
                        <span class="text-xs text-slate-400">
                            {{ $inst->category->name ?? '' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $inst->id }})" class="text-blue-500 hover:text-blue-700 text-sm font-bold">
                            Ред.
                        </button>
                        <button wire:click="delete({{ $inst->id }})" wire:confirm="Удалить инструкцию?" class="text-red-500 hover:text-red-700 text-sm font-bold">
                            Удалить
                        </button>
                    </div>
                </div>

                <ul class="space-y-1 text-sm text-slate-700">
                    @foreach($inst->steps ?? [] as $step)
                        <li class="flex gap-2">
                            <span class="text-emerald-600 font-bold">•</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="text-center text-slate-400 py-10">
                Инструкции пока не добавлены
            </div>
        @endforelse
    </div>
</div>