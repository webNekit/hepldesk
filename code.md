# Файлы компонентов Livewire (Админка и Клиент)

## app/Livewire/Admin/CategoryManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\User;
use Livewire\Component;

class CategoryManagement extends Component
{
    public $name;
    public $selectedTechnicians = [];
    public $editingCategoryId;
    public $showCategoryModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'selectedTechnicians' => 'required|array|min:1',
    ];

    public function openCategoryModal() { $this->showCategoryModal = true; }
    
    public function closeCategoryModal() { 
        $this->showCategoryModal = false; 
        $this->reset(['name', 'selectedTechnicians', 'editingCategoryId']); 
    }

    public function render()
    {
        return view('livewire.admin.category-management', [
            'categories' => Category::with(['instructions', 'parts', 'technicians'])
                                     ->withCount(['parts', 'instructions', 'tickets'])
                                     ->get(),
            'it_supports' => User::role('it_support')->get(),
        ])->layout('layouts.admin');
    }

    public function createCategory()
    {
        $this->validate();
        $category = Category::create(['name' => $this->name]);
        $category->technicians()->sync($this->selectedTechnicians);
        $this->closeCategoryModal();
    }

    public function editCategory($id)
    {
        $category = Category::with('technicians')->findOrFail($id);
        $this->editingCategoryId = $id;
        $this->name = $category->name;
        $this->selectedTechnicians = $category->technicians->pluck('id')->toArray();
        $this->openCategoryModal();
    }

    public function updateCategory()
    {
        $this->validate();
        $category = Category::find($this->editingCategoryId);
        $category->update(['name' => $this->name]);
        $category->technicians()->sync($this->selectedTechnicians);
        $this->closeCategoryModal();
    }
}

## app/Livewire/Admin/PartManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\Part;
use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class PartManagement extends Component
{
    use WithPagination;

    public $name, $sku, $category_id, $brand_id, $quantity, $description, $editingPartId;
    public $showPartModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'required|exists:brands,id',
        'quantity' => 'required|integer|min:0',
    ];

    public function openPartModal() { $this->showPartModal = true; }
    public function closePartModal() { 
        $this->showPartModal = false; 
        $this->reset(['name', 'sku', 'category_id', 'brand_id', 'quantity', 'description', 'editingPartId']);
    }

    public function render()
    {
        return view('livewire.admin.part-management', [
            'parts' => Part::with(['category', 'brand'])->paginate(10),
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ])->layout('layouts.admin');
    }

    public function createPart()
    {
        $this->validate();
        Part::create([
            'name' => $this->name,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'quantity' => $this->quantity,
            'description' => $this->description,
        ]);
        $this->closePartModal();
    }

    public function editPart($id)
    {
        $part = Part::findOrFail($id);
        $this->editingPartId = $id;
        $this->name = $part->name;
        $this->sku = $part->sku;
        $this->category_id = $part->category_id;
        $this->brand_id = $part->brand_id;
        $this->quantity = $part->quantity;
        $this->description = $part->description;
        $this->openPartModal();
    }

    public function updatePart()
    {
        $this->validate();
        Part::findOrFail($this->editingPartId)->update([
            'name' => $this->name,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'quantity' => $this->quantity,
            'description' => $this->description,
        ]);
        $this->closePartModal();
    }
}

## app/Livewire/Admin/EmployeeManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;

class EmployeeManagement extends Component
{
    public $name, $email, $department_id, $position, $phone, $cabinet, $editingEmployeeId;
    public $showModal = false;

    public function openModal() { $this->showModal = true; }
    public function closeModal() { 
        $this->showModal = false; 
        $this->reset(['name', 'email', 'department_id', 'position', 'phone', 'cabinet', 'editingEmployeeId']); 
    }

    public function render()
    {
        return view('livewire.admin.employee-management', [
            'employees' => User::with('department')->get(),
            'departments' => Department::all(),
        ])->layout('layouts.admin');
    }

    public function edit($id) {
        $employee = User::findOrFail($id);
        $this->editingEmployeeId = $id;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->department_id = $employee->department_id;
        $this->position = $employee->position;
        $this->phone = $employee->phone;
        $this->cabinet = $employee->cabinet;
        $this->openModal();
    }

    public function save() {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->editingEmployeeId ?? 'NULL'),
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'phone' => 'nullable',
            'cabinet' => 'nullable'
        ]);

        if ($this->editingEmployeeId) {
            User::find($this->editingEmployeeId)->update($data);
        } else {
            $data['password'] = bcrypt('password');
            User::create($data)->assignRole('employee');
        }
        $this->closeModal();
    }
}

## app/Livewire/Admin/DirectoryManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use Livewire\Component;

class DirectoryManagement extends Component
{
    public $name, $editingDeptId;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function edit($id)
    {
        $dept = Department::findOrFail($id);
        $this->editingDeptId = $id;
        $this->name = $dept->name;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingDeptId) {
            Department::find($this->editingDeptId)->update(['name' => $this->name]);
            session()->flash('message', 'Отдел обновлен!');
        } else {
            Department::create(['name' => $this->name]);
            session()->flash('message', 'Отдел создан!');
        }

        $this->reset(['name', 'editingDeptId']);
    }

    public function delete($id)
    {
        Department::find($id)->delete();
        session()->flash('message', 'Отдел удален!');
    }

    public function render()
    {
        return view('livewire.admin.directory-management', [
            'departments' => Department::all(),
        ])->layout('layouts.admin');
    }
}

## app/Livewire/Admin/UserManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    public $name, $email, $password, $department_id, $position, $phone, $cabinet, $selected_roles = [];
    public $editingUserId, $showModal = false;

    public function openModal() { $this->showModal = true; }
    public function closeModal() { 
        $this->showModal = false; 
        $this->reset(['name', 'email', 'password', 'department_id', 'position', 'phone', 'cabinet', 'selected_roles', 'editingUserId']); 
    }

    public function getRoleDisplayName($roleName)
    {
        return [
            'employee' => 'Сотрудник',
            'it_support' => 'Техподдержка',
            'manager' => 'Менеджер',
            'admin' => 'Администратор',
        ][$roleName] ?? $roleName;
    }

    public function render() {
        return view('livewire.admin.user-management', [
            'users' => User::with('roles', 'department')->get(),
            'departments' => Department::all(),
            'roles' => Role::all(),
        ])->layout('layouts.admin');
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department_id = $user->department_id;
        $this->position = $user->position;
        $this->selected_roles = $user->roles->pluck('name')->toArray();
        $this->openModal();
    }

    public function save() {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->editingUserId ?? 'NULL'),
            'selected_roles' => 'required|array|min:1',
        ];
        if (!$this->editingUserId) $rules['password'] = 'required|min:6';
        
        $data = $this->validate($rules);

        if ($this->editingUserId) {
            $user = User::find($this->editingUserId);
            $user->update($data);
            $user->syncRoles($this->selected_roles);
        } else {
            $data['password'] = bcrypt($this->password);
            $user = User::create($data);
            $user->assignRole($this->selected_roles);
        }
        $this->closeModal();
    }
}

## app/Livewire/Admin/ResourceManagement.php
<?php

namespace App\Livewire\Admin;

use App\Models\GovernmentResource;
use Livewire\Component;

class ResourceManagement extends Component
{
    public $name, $url, $editingResourceId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'url' => 'required|url',
    ];

    public function edit($id)
    {
        $res = GovernmentResource::findOrFail($id);
        $this->editingResourceId = $id;
        $this->name = $res->name;
        $this->url = $res->url;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingResourceId) {
            GovernmentResource::find($this->editingResourceId)->update([
                'name' => $this->name,
                'url' => $this->url,
            ]);
            session()->flash('message', 'Ресурс обновлен!');
        } else {
            GovernmentResource::create([
                'name' => $this->name,
                'url' => $this->url,
            ]);
            session()->flash('message', 'Ресурс добавлен!');
        }

        $this->reset(['name', 'url', 'editingResourceId']);
    }

    public function delete($id)
    {
        GovernmentResource::find($id)->delete();
        session()->flash('message', 'Ресурс удален!');
    }

    public function render()
    {
        return view('livewire.admin.resource-management', [
            'resources' => GovernmentResource::all(),
        ])->layout('layouts.admin');
    }
}

## app/Livewire/PdfInstructions.php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;
use Livewire\WithFileUploads;

class PdfInstructions extends Component
{
    use WithFileUploads;

    public $title, $category_id, $pdf, $editingId, $showModal = false;

    public function openModal() { $this->showModal = true; }
    public function closeModal() { $this->reset(); $this->showModal = false; }

    public function save() {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'pdf' => 'required|file|mimes:pdf']);
        $path = $this->pdf->store('instructions', 'public');
        Instruction::create(['title' => $this->title, 'category_id' => $this->category_id, 'pdf_path' => $path]);
        $this->closeModal();
    }

    public function render() {
        return view('livewire.pdf-instructions', [
            'instructions' => Instruction::whereNotNull('pdf_path')->get(),
            'categories' => Category::all()
        ])->layout('layouts.admin');
    }
}

## app/Livewire/ManageInstructions.php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class ManageInstructions extends Component
{
    public $title, $category_id, $steps = [''], $editingId;
    public $showModal = false;

    public function addStep() { $this->steps[] = ''; }
    public function removeStep($index) { unset($this->steps[$index]); $this->steps = array_values($this->steps); }

    public function openModal() { $this->showModal = true; }
    public function closeModal() { $this->reset(['title', 'category_id', 'steps', 'editingId']); $this->showModal = false; }

    public function save() {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'steps' => 'required|array']);
        Instruction::updateOrCreate(['id' => $this->editingId], [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'steps' => $this->steps
        ]);
        $this->closeModal();
    }

    public function render() {
        return view('livewire.manage-instructions', [
            'instructions' => Instruction::whereNull('pdf_path')->with('category')->get(),
            'categories' => Category::all()
        ])->layout('layouts.admin');
    }
}

## app/Livewire/CreateTicket.php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use App\Models\Ticket;
use Livewire\Component;

class CreateTicket extends Component
{
    public $categories, $category_id, $instruction, $title, $description, $priority = 'normal';
    public $contact_name, $contact_phone, $contact_email;

    public function mount() { $this->categories = Category::all(); }

    public function updatedCategoryId($value)
    {
        // Клиенты видят инструкции БЕЗ pdf_path (текстовые)
        $this->instruction = $value ? Instruction::where('category_id', $value)->whereNull('pdf_path')->first() : null;
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'contact_email' => 'required|email',
        ]);

        $category = Category::with('technicians')->find($this->category_id);
        $assignedTo = $category->technicians()->withCount(['assignedTickets' => fn($q) => $q->whereIn('status', ['new', 'in_progress'])])
                        ->orderBy('assigned_tickets_count', 'asc')->first()?->id;

        Ticket::create([
            'user_id' => auth()->id() ?? 1,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assigned_to' => $assignedTo,
            'status' => 'new',
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
        ]);

        session()->flash('message', 'Заявка создана!');
        return redirect()->to('/');
    }

    public function render() { return view('livewire.create-ticket')->layout('layouts.app'); }
}

## app/Livewire/TicketDetail.php
<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Instruction;
use App\Models\Ticket;
use App\Models\Part;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;
    public $body;
    public $instruction;
    public $showInstruction = false;
    public $part_id;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->instruction = Instruction::where('category_id', $ticket->category_id)->first();
    }

    public function getAvailablePartsProperty()
    {
        return Part::where('category_id', $this->ticket->category_id)->get();
    }

    public function attachPart()
    {
        if (!$this->part_id) return;
        
        $part = Part::findOrFail($this->part_id);
        if ($part->quantity > 0) {
            $part->decrement('quantity');
            $this->ticket->comments()->create([
                'user_id' => auth()->id(),
                'body' => "Использована запчасть: " . $part->name
            ]);
            session()->flash('message', 'Запчасть успешно списана!');
        } else {
            session()->flash('error', 'Запчасть закончилась!');
        }
    }

    public function updateStatus($status)
    {
        $this->ticket->update(['status' => $status]);
        session()->flash('message', 'Статус обновлен!');
    }

    public function render()
    {
        return view('livewire.ticket-detail', [
            'parts' => $this->availableParts
        ])->layout('layouts.admin');
    }
}

# Файлы представлений (Views)

## resources/views/livewire/admin/category-management.blade.php
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

## resources/views/livewire/admin/part-management.blade.php
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

## resources/views/livewire/admin/employee-management.blade.php
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Сотрудники</h2>
        <button wire:click="openModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            ДОБАВИТЬ СОТРУДНИКА
        </button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ $editingEmployeeId ? 'Редактировать сотрудника' : 'Новый сотрудник' }}</h3>
                <form wire:submit.prevent="save" class="space-y-4">
                    <input type="text" wire:model.live="name" placeholder="ФИО" class="w-full rounded-lg border-slate-300">
                    <input type="email" wire:model.live="email" placeholder="Email" class="w-full rounded-lg border-slate-300">
                    <select wire:model.live="department_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->name }}</option> @endforeach
                    </select>
                    <input type="text" wire:model.live="position" placeholder="Должность" class="w-full rounded-lg border-slate-300">
                    <input type="text" wire:model.live="phone" placeholder="Телефон" class="w-full rounded-lg border-slate-300">
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeModal" class="bg-slate-100 text-slate-500 font-bold py-2 px-4 rounded-lg">Отмена</button>
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
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Имя</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Отдел</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($employees as $employee)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">{{ $employee->name }}</td>
                        <td class="px-6 py-4">{{ optional($employee->department)->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $employee->id }})" class="text-blue-600 font-bold text-xs uppercase">Изменить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

## resources/views/livewire/admin/directory-management.blade.php
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление разделами справочника</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingDeptId ? 'Редактировать' : 'Новый' }} раздел (отдел)</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Название отдела</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all">
                    {{ $editingDeptId ? 'ОБНОВИТЬ' : 'СОХРАНИТЬ' }}
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Название</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($departments as $dept)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-emerald-900">{{ $dept->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $dept->id }})" class="text-blue-600 hover:text-blue-900 mr-4 font-bold text-xs uppercase">Изменить</button>
                                    <button onclick="confirm('Удалить?') || event.stopImmediatePropagation()" wire:click="delete({{ $dept->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Удалить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

## resources/views/livewire/admin/user-management.blade.php
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление пользователями</h2>
        <button wire:click="openModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
            ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ
        </button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ $editingUserId ? 'Редактировать пользователя' : 'Новый пользователь' }}</h3>
                <form wire:submit.prevent="save" class="space-y-4">
                    <input type="text" wire:model.live="name" placeholder="ФИО" class="w-full rounded-lg border-slate-300">
                    <input type="email" wire:model.live="email" placeholder="Email" class="w-full rounded-lg border-slate-300">
                    @if(!$editingUserId) <input type="password" wire:model.live="password" placeholder="Пароль" class="w-full rounded-lg border-slate-300"> @endif
                    
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Роли</label>
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="selected_roles" value="{{ $role->name }}"> 
                                {{ $this->getRoleDisplayName($role->name) }}
                            </label>
                        @endforeach
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="bg-slate-100 text-slate-500 font-bold py-2 px-4 rounded-lg">Отмена</button>
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
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ФИО</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Роли</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-emerald-900">{{ $user->name }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded-full mr-1">
                                    {{ $this->getRoleDisplayName($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $user->id }})" class="text-blue-600 font-bold text-xs uppercase">Изменить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

## resources/views/livewire/pdf-instructions.blade.php
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

## resources/views/livewire/manage-instructions.blade.php
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

## resources/views/livewire/create-ticket.blade.php
<div class="max-w-4xl mx-auto py-12 px-6">
    <h2 class="text-3xl font-bold mb-8 text-slate-800">Создать заявку</h2>
    
    <form wire:submit.prevent="save" class="space-y-6 bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="text" wire:model="contact_name" placeholder="ФИО" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
            <input type="text" wire:model="contact_phone" placeholder="Телефон" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
            <input type="email" wire:model="contact_email" placeholder="Email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
            <select wire:model.live="category_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                <option value="">Выберите категорию</option>
                @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
            </select>
        </div>

        <input type="text" wire:model="title" placeholder="Тема заявки" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
        <textarea wire:model="description" placeholder="Описание проблемы" rows="4" class="w-full rounded-lg border-slate-300 focus:border-emerald-500"></textarea>

        @if($instruction)
            <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-xl">
                <h4 class="font-bold text-emerald-900 mb-4">{{ $instruction->title }}</h4>
                <ul class="list-disc pl-5 text-sm text-slate-700 space-y-1">
                    @foreach($instruction->steps as $step) <li>{{ $step }}</li> @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-lg">
            ОТПРАВИТЬ ЗАЯВКУ
        </button>
    </form>
</div>

## resources/views/livewire/ticket-detail.blade.php
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

# База данных

## database/seeders/DatabaseSeeder.php
<?php

namespace Database\Seeders;

use App\Models\{Category, Department, Instruction, Part, Brand, GovernmentResource, User};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Роли и отделы
        foreach (['admin', 'it_support', 'manager', 'employee'] as $r) Role::firstOrCreate(['name' => $r]);
        $deptIt = Department::firstOrCreate(['name' => 'ИТ-отдел']);
        $deptAcc = Department::firstOrCreate(['name' => 'Бухгалтерия']);
        $deptHr = Department::firstOrCreate(['name' => 'Отдел кадров']);

        // 2. Пользователи
        $users = [
            ['admin', 'Иванов Иван Иванович', 'admin1@mats.ru', $deptIt],
            ['admin', 'Петров Петр Петрович', 'admin2@mats.ru', $deptIt],
            ['it_support', 'Сидоров Сергей Сергеевич', 'tech1@mats.ru', $deptIt],
            ['it_support', 'Кузнецова Анна Павловна', 'tech2@mats.ru', $deptIt],
            ['it_support', 'Волков Владимир Игоревич', 'tech3@mats.ru', $deptIt],
            ['it_support', 'Соловьева Ольга Игоревна', 'tech4@mats.ru', $deptIt],
            ['manager', 'Лебедева Татьяна Сергеевна', 'manager1@mats.ru', $deptAcc],
            ['employee', 'Попов Дмитрий Алексеевич', 'employee1@mats.ru', $deptHr],
            ['employee', 'Васильева Ольга Николаевна', 'employee2@mats.ru', $deptHr],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(['email' => $u[2]], [
                'name' => $u[1],
                'password' => bcrypt('password'),
                'department_id' => $u[3]->id,
                'position' => 'Специалист',
            ]);
            $user->assignRole($u[0]);
        }

        // 3. Данные для модулей
        $brands = ['HP', 'Lenovo', 'Cisco', 'Xerox', 'Samsung', 'Dell'];
        foreach ($brands as $b) Brand::firstOrCreate(['name' => $b], ['slug' => strtolower($b)]);

        $techs = User::role('it_support')->get();
        $categories = [
            'Не включается компьютер' => ['Блок питания ATX 500W', 'Кабель питания 220V'],
            'Не печатает принтер' => ['Картридж HP 107A', 'Ролик захвата бумаги'],
            'Нет доступа к сети' => ['Патч-корд RJ-45 2м', 'Сетевая карта PCI-E'],
            'Проблемы с монитором' => ['Кабель HDMI-HDMI', 'Матрица 21.5"'],
        ];

        foreach ($categories as $catName => $parts) {
            $cat = Category::firstOrCreate(['name' => $catName], ['default_assignee_id' => $techs->first()->id]);
            // Привязываем случайных техников к категории
            $cat->technicians()->sync($techs->random(2)->pluck('id'));
            
            Instruction::firstOrCreate(['title' => "Инструкция: $catName", 'category_id' => $cat->id], [
                'steps' => ['Проверить подключение', 'Перезагрузить устройство', 'Проверить настройки', 'Создать обращение']
            ]);

            foreach ($parts as $pName) {
                Part::firstOrCreate(['name' => $pName, 'category_id' => $cat->id], [
                    'sku' => strtoupper(substr(str_replace(' ', '', $pName), 0, 5)) . '-' . rand(100, 999),
                    'brand_id' => Brand::inRandomOrder()->first()->id,
                    'quantity' => rand(5, 30)
                ]);
            }
        }

        GovernmentResource::firstOrCreate(['name' => 'Госуслуги', 'url' => 'https://www.gosuslugi.ru']);
        GovernmentResource::firstOrCreate(['name' => 'Минцифры РФ', 'url' => 'https://digital.gov.ru']);
    }
}

# Модели (Models)

## app/Models/Category.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'default_assignee_id'])]
class Category extends Model
{
    use HasFactory;

    public function technicians()
    {
        return $this->belongsToMany(User::class);
    }

    public function defaultAssignee()
    {
        return $this->belongsTo(User::class, 'default_assignee_id');
    }

    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}

## app/Models/Instruction.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_id', 'title', 'steps'])]
class Instruction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'steps' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

## app/Models/Part.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model
{
    protected $fillable = ['name', 'sku', 'category_id', 'brand_id', 'quantity', 'description'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}

## app/Models/Ticket.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'category_id', 'assigned_to', 'title', 'description', 'status', 'priority'])]
class Ticket extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

## app/Models/User.php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'department_id', 'position', 'phone', 'cabinet'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }
}

## app/Models/Brand.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }
}

## app/Models/Department.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Department extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

## app/Models/Comment.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ticket_id', 'user_id', 'body'])]
class Comment extends Model
{
    use HasFactory;

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

## app/Models/GovernmentResource.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'url'])]
class GovernmentResource extends Model
{
    use HasFactory;
}

# Настройки и Маршруты

## routes/web.php
<?php

use App\Livewire\CreateTicket;
use App\Livewire\DirectoryTable;
use App\Livewire\ItDashboard;
use App\Livewire\TicketDetail;
use App\Livewire\ManageInstructions;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\ResourceManagement;
use App\Livewire\Admin\DirectoryManagement;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'resources' => \App\Models\GovernmentResource::all()
    ]);
})->name('home');

Route::get('/directory', function () {
    return view('directory');
})->name('directory');

Route::get('/support', CreateTicket::class)->name('support');

Route::middleware(['auth'])->group(function () {
    // Shared for Technicians and Admins
    Route::middleware(['role:it_support|admin'])->group(function () {
        Route::get('/it-dashboard', ItDashboard::class)->name('it-dashboard');
        Route::get('/tickets/{ticket}', TicketDetail::class)->name('ticket-detail');
        Route::get('/manage-instructions', ManageInstructions::class)->name('manage-instructions');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/categories', App\Livewire\Admin\CategoryManagement::class)->name('admin.categories');
        Route::get('/admin/parts', App\Livewire\Admin\PartManagement::class)->name('admin.parts');
        Route::get('/admin/brands', App\Livewire\Admin\BrandManagement::class)->name('admin.brands');
        Route::get('/admin/pdf-instructions', App\Livewire\PdfInstructions::class)->name('admin.pdf-instructions');
    });

    // Manager and Admin
    Route::middleware(['role:manager|admin'])->group(function () {
        Route::get('/admin/users', UserManagement::class)->name('admin.users');
        Route::get('/admin/employees', App\Livewire\Admin\EmployeeManagement::class)->name('admin.employees');
        Route::get('/admin/directory', DirectoryManagement::class)->name('admin.directory');
        Route::get('/admin/resources', ResourceManagement::class)->name('admin.resources');
    });
});

// Demo Login Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function () {
    $user = User::where('email', request('email'))->first();
    if ($user) {
        auth()->login($user);
        
        // Redirect based on user role
        if (auth()->user()->hasRole('admin')) {
            return redirect()->intended('/admin/users'); // Admin dashboard
        } elseif (auth()->user()->hasRole('it_support')) {
            return redirect()->intended('/it-dashboard'); // IT dashboard
        } elseif (auth()->user()->hasRole('manager')) {
            return redirect()->intended('/admin/directory'); // Manager dashboard (directory management)
        } else {
            return redirect()->intended('/'); // Regular employee goes to home
        }
    }
    return back()->withErrors(['email' => 'User not found']);
});

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

## resources/views/layouts/admin.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-['Public_Sans'] antialiased">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-emerald-950 text-white flex-shrink-0 flex flex-col shadow-xl">
            <div class="h-20 flex items-center px-6 border-b border-emerald-900">
                <span class="text-xl font-bold tracking-wider">ГКУ ВО "МАЦ"</span>
            </div>
            
            <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
                <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-900 rounded-lg transition-colors mb-6">
                    <span class="material-symbols-outlined">arrow_back</span> На сайт
                </a>

                <div class="text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Основное</div>
                
                @role('admin|it_support')
                <a href="/it-dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('it-dashboard*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">dashboard</span> Дашборд заявок
                </a>
                @endrole

                @role('admin|manager')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Администрирование</div>
                <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/users*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">group</span> Пользователи
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/categories*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">category</span> Категории
                </a>
                @endrole

                @role('admin|it_support')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Справочник</div>
                <a href="/manage-instructions" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('manage-instructions*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">menu_book</span> Инструкции (клиенты)
                </a>
                <a href="/admin/pdf-instructions" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/pdf-instructions*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">picture_as_pdf</span> PDF (сотрудники)
                </a>
                @endrole

                @role('admin|manager')
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Контент</div>
                <a href="/admin/employees" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/employees*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">badge</span> Сотрудники
                </a>
                <a href="/admin/directory" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/directory*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">contact_phone</span> Отделы
                </a>
                <div class="mt-8 text-xs font-bold text-emerald-500 uppercase px-4 mb-2 tracking-widest">Склад</div>
                <a href="/admin/brands" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/brands*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">inventory</span> Бренды
                </a>
                <a href="/admin/parts" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/parts*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">build</span> Запчасти
                </a>
                @endrole
            </nav>
        </aside>

        <div class="flex-grow flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
                <h1 class="text-xl font-bold text-slate-800">@yield('header_title', 'Панель управления')</h1>
                <form method="POST" action="/logout">@csrf <button class="text-slate-500">Выход</button></form>
            </header>
            <main class="flex-grow overflow-y-auto p-8">
                {{ $slot ?? '' }} @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

## database/migrations/2026_04_23_204738_add_pdf_path_and_contacts_to_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'contact_email']);
        });
    }
};
