<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">PDF инструкции для сотрудников</h2>
        <button wire:click="openModal" class="bg-emerald-900 text-white px-4 py-2 rounded-lg">ЗАГРУЗИТЬ PDF</button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <form wire:submit.prevent="save" class="space-y-4">
                    <input type="text" wire:model="title" placeholder="Название" class="w-full border rounded p-2">
                    <select wire:model="category_id" class="w-full border rounded p-2">
                        <option>Категория</option>
                        @foreach($categories as $c) <option value="{{$c->id}}">{{$c->name}}</option> @endforeach
                    </select>
                    <input type="file" wire:model="pdf" class="w-full">
                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="bg-gray-200 px-4 py-2 rounded">Отмена</button>
                        <button type="submit" class="bg-emerald-900 text-white px-4 py-2 rounded">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead><tr class="bg-gray-100"><th class="p-4">Название</th><th class="p-4">Скачать</th></tr></thead>
            <tbody>
                @foreach($instructions as $inst)
                <tr class="border-t">
                    <td class="p-4">{{ $inst->title }}</td>
                    <td class="p-4"><a href="{{ asset('storage/'.$inst->pdf_path) }}" class="text-blue-600 underline">PDF</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>