<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">
            Инструкции для техников (PDF)
        </h2>

        <button wire:click="openModal"
            class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            ДОБАВИТЬ PDF
        </button>
    </div>

    {{-- МОДАЛКА --}}
    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-8">

                <h3 class="text-xl font-bold text-slate-800 mb-6">
                    {{ $editingId ? 'Редактирование PDF инструкции' : 'Загрузка PDF инструкции' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-6">

                    {{-- Название --}}
                    <input type="text" wire:model="title" placeholder="Название инструкции"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500">

                    {{-- Категория --}}
                    <select wire:model="category_id" class="w-full rounded-xl border-slate-300 focus:border-emerald-500">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>

                    {{-- ФАЙЛ --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-600">
                            PDF файл
                        </label>

                        <input type="file" wire:model="pdf" accept="application/pdf" class="w-full text-sm">

                        @if($pdf)
                            <div class="text-xs text-emerald-600 font-bold">
                                Файл выбран: {{ $pdf->getClientOriginalName() }}
                            </div>
                        @endif

                        @error('pdf')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- КНОПКИ --}}
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                            class="bg-slate-100 text-slate-600 font-bold py-2 px-5 rounded-xl">
                            Отмена
                        </button>

                        <button type="submit"
                            class="bg-emerald-900 text-white font-bold py-2 px-5 rounded-xl hover:bg-emerald-800">
                            Сохранить
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    {{-- СПИСОК --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                        Название
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                        Категория
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">
                        Действие
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($instructions as $inst)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">
                            {{ $inst->title }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $inst->category->name ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-right flex justify-end gap-3 items-center">
                            <a href="{{ asset('storage/' . $inst->pdf_path) }}" target="_blank"
                                class="text-emerald-600 hover:text-emerald-800 font-bold text-sm uppercase">
                                Открыть
                            </a>
                            <button wire:click="edit({{ $inst->id }})" class="text-blue-500 hover:text-blue-700 text-sm font-bold">
                                Ред.
                            </button>
                            <button wire:click="delete({{ $inst->id }})" wire:confirm="Удалить инструкцию?" class="text-red-500 hover:text-red-700 text-sm font-bold">
                                Удалить
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-10 text-slate-400">
                            PDF инструкции не добавлены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>