<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление запчастями</h2>
        <button wire:click="openPartModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            НОВАЯ ЗАПЧАСТЬ
        </button>
    </div>

    @if($showPartModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingPartId ? 'Редактировать запчасть' : 'Новая запчасть' }}</h3>
                <form wire:submit.prevent="{{ $editingPartId ? 'updatePart' : 'createPart' }}" class="space-y-4">
                    <input type="text" wire:model.live="name" placeholder="Название" class="w-full rounded-lg border-slate-300">
                    <input type="text" wire:model.live="sku" placeholder="Артикул (SKU)" class="w-full rounded-lg border-slate-300">
                    
                    <select wire:model.live="category_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                    </select>

                    <select wire:model.live="brand_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Выберите бренд</option>
                        @foreach($brands as $brand) <option value="{{ $brand->id }}">{{ $brand->name }}</option> @endforeach
                    </select>

                    <input type="number" wire:model.live="quantity" placeholder="Количество" class="w-full rounded-lg border-slate-300">
                    <textarea wire:model.live="description" placeholder="Описание" class="w-full rounded-lg border-slate-300"></textarea>
                    
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" wire:click="closePartModal" class="bg-slate-100 text-slate-500 font-bold py-2 px-4 rounded-lg">Отмена</button>
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
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Название</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Категория</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Кол-во</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($parts as $part)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">{{ $part->name }}</td>
                        <td class="px-6 py-4">{{ optional($part->category)->name }}</td>
                        <td class="px-6 py-4">{{ $part->quantity }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="editPart({{ $part->id }})" class="text-blue-600 font-bold text-xs uppercase">Изменить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>