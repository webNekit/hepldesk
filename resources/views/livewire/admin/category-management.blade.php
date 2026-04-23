<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление категориями</h2>
        <button wire:click="openCategoryModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            НОВАЯ КАТЕГОРИЯ
        </button>
    </div>

    @if($showCategoryModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingCategoryId ? 'Редактировать категорию' : 'Новая категория' }}</h3>
                <form wire:submit.prevent="{{ $editingCategoryId ? 'updateCategory' : 'createCategory' }}" class="space-y-4">
                    <input type="text" wire:model.live="name" placeholder="Название категории" class="w-full rounded-lg border-slate-300">
                    <div class="space-y-1 mt-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-2">
                        @foreach($it_supports as $tech)
                            <label class="flex items-center gap-2 text-sm text-slate-600 p-1">
                                <input type="checkbox" wire:model="selectedTechnicians" value="{{ $tech->id }}">
                                {{ $tech->name }}
                            </label>
                        @endforeach
                    </div>
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" wire:click="closeCategoryModal" class="bg-slate-100 text-slate-500 font-bold py-2 px-4 rounded-lg">Отмена</button>
                        <button type="submit" class="bg-emerald-900 text-white font-bold py-2 px-4 rounded-lg">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Категория</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Техники</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Инструкции</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($categories as $category)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $category->technicians->pluck('name')->implode(', ') ?: 'Нет техников' }}
                        </td>
                        <td class="px-6 py-4">
                            @foreach($category->instructions as $inst)
                                <div class="text-sm text-blue-600 cursor-pointer hover:underline mb-1">
                                    {{ $inst->title }}
                                </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="editCategory({{ $category->id }})" class="text-blue-600 font-bold text-xs uppercase">Изменить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>