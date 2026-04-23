<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Инструкции для клиентов (Шаги)</h2>
        <button wire:click="openModal" class="bg-emerald-900 text-white px-4 py-2 rounded-lg">ДОБАВИТЬ ИНСТРУКЦИЮ</button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-lg w-full max-w-lg">
                <form wire:submit.prevent="save" class="space-y-4">
                    <input type="text" wire:model="title" placeholder="Название" class="w-full border rounded p-2">
                    <select wire:model="category_id" class="w-full border rounded p-2">
                        <option value="">Категория</option>
                        @foreach($categories as $c) <option value="{{$c->id}}">{{$c->name}}</option> @endforeach
                    </select>
                    
                    <div class="space-y-2">
                        @foreach($steps as $index => $step)
                            <div class="flex gap-2">
                                <input type="text" wire:model="steps.{{$index}}" class="w-full border rounded p-2">
                                <button type="button" wire:click="removeStep({{$index}})" class="text-red-500">X</button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addStep" class="text-emerald-700 underline text-sm">+ Шаг</button>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="bg-gray-200 px-4 py-2 rounded">Отмена</button>
                        <button type="submit" class="bg-emerald-900 text-white px-4 py-2 rounded">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        @foreach($instructions as $inst)
            <div class="mb-4 p-4 border rounded">
                <h4 class="font-bold">{{ $inst->title }}</h4>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($inst->steps as $step) <li>{{ $step }}</li> @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>