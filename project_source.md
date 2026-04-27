# FULL PROJECT CODE
## File: app/Models/Asset.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'type',
        'user_id',
        'department_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
```

## File: app/Models/Brand.php
```php
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
```

## File: app/Models/Category.php
```php
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
```

## File: app/Models/Comment.php
```php
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
```

## File: app/Models/Department.php
```php
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
```

## File: app/Models/GovernmentResource.php
```php
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
```

## File: app/Models/Instruction.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_id', 'title', 'steps', 'pdf_path'])]
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
```

## File: app/Models/Part.php
```php
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
```

## File: app/Models/Ticket.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['user_id', 'category_id', 'assigned_to', 'title', 'description', 'status', 'priority', 'contact_name', 'contact_phone', 'contact_email', 'uuid', 'due_date', 'rating', 'feedback_comment', 'asset_id'])]
class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'due_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_to', 'priority', 'due_date'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function activities()
    {
        return $this->activitiesAsSubject();
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
```

## File: app/Models/User.php
```php
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
```

## File: app/Livewire/Admin/AdminDashboard.php
```php
<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\Category;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        $stats = [
            'tickets_new' => Ticket::where('status', 'new')->count(),
            'tickets_total' => Ticket::count(),
            'users_total' => User::count(),
            'departments_total' => Department::count(),
        ];

        $recentTickets = Ticket::with(['user', 'category'])->latest()->take(5)->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/Admin/AnalyticsDashboard.php
```php
<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public function render()
    {
        $stats = [
            'total' => Ticket::count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'pending' => Ticket::whereIn('status', ['new', 'in_progress'])->count(),
            'avg_rating' => round(Ticket::whereNotNull('rating')->avg('rating'), 1),
        ];

        $statusDistribution = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $techPerformance = User::role('it_support')
            ->withCount(['assignedTickets as resolved_count' => function ($query) {
                $query->where('status', 'resolved');
            }])
            ->orderBy('resolved_count', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.analytics-dashboard', [
            'stats' => $stats,
            'statusDistribution' => $statusDistribution,
            'techPerformance' => $techPerformance,
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/Admin/AssetManagement.php
```php
<?php

namespace App\Livewire\Admin;

use App\Models\Asset;
use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AssetManagement extends Component
{
    use WithPagination;

    public $name;

    public $serial_number;

    public $type;

    public $user_id;

    public $department_id;

    public $status = 'active';

    public $editingAssetId;

    public $showAssetModal = false;

    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'serial_number' => 'nullable|string|max:255',
        'type' => 'required|string',
        'user_id' => 'nullable|exists:users,id',
        'department_id' => 'nullable|exists:departments,id',
        'status' => 'required|in:active,repair,retired',
    ];

    public function openAssetModal()
    {
        $this->showAssetModal = true;
    }

    public function closeAssetModal()
    {
        $this->showAssetModal = false;
        $this->reset(['name', 'serial_number', 'type', 'user_id', 'department_id', 'status', 'editingAssetId']);
    }

    public function save()
    {
        $this->validate();

        if ($this->editingAssetId) {
            Asset::find($this->editingAssetId)->update([
                'name' => $this->name,
                'serial_number' => $this->serial_number,
                'type' => $this->type,
                'user_id' => $this->user_id,
                'department_id' => $this->department_id,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Оборудование обновлено!');
        } else {
            Asset::create([
                'name' => $this->name,
                'serial_number' => $this->serial_number,
                'type' => $this->type,
                'user_id' => $this->user_id,
                'department_id' => $this->department_id,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Оборудование добавлено!');
        }

        $this->closeAssetModal();
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $this->editingAssetId = $id;
        $this->name = $asset->name;
        $this->serial_number = $asset->serial_number;
        $this->type = $asset->type;
        $this->user_id = $asset->user_id;
        $this->department_id = $asset->department_id;
        $this->status = $asset->status;
        $this->openAssetModal();
    }

    public function delete($id)
    {
        Asset::findOrFail($id)->delete();
        session()->flash('message', 'Оборудование удалено!');
    }

    public function render()
    {
        $assets = Asset::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('serial_number', 'like', '%'.$this->search.'%');
            })
            ->with(['user', 'department'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.asset-management', [
            'assets' => $assets,
            'departments' => Department::all(),
            'users' => User::all(),
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/Admin/BrandManagement.php
```php
<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use Livewire\Component;

class BrandManagement extends Component
{
    public $brands;
    public $name;
    public $slug;
    public $description;
    public $editingBrandId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:100|unique:brands,slug',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->brands = Brand::all();
    }

    public function render()
    {
        return view('livewire.admin.brand-management', [
            'brands' => Brand::withCount('parts')->get(),
        ])->layout('layouts.admin');
    }

    public function createBrand()
    {
        $this->validate();

        Brand::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Бренд создан!');
        $this->reset(['name', 'slug', 'description']);
        $this->brands = Brand::all();
    }

    public function editBrand($id)
    {
        $brand = Brand::findOrFail($id);
        $this->editingBrandId = $id;
        $this->name = $brand->name;
        $this->slug = $brand->slug;
        $this->description = $brand->description;
    }

    public function updateBrand()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:brands,slug,' . $this->editingBrandId,
            'description' => 'nullable|string',
        ]);

        Brand::find($this->editingBrandId)->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Бренд обновлен!');
        $this->reset(['name', 'slug', 'description', 'editingBrandId']);
        $this->brands = Brand::all();
    }

    public function deleteBrand($id)
    {
        Brand::find($id)->delete();
        session()->flash('message', 'Бренд удален!');
        $this->brands = Brand::all();
    }
}```

## File: app/Livewire/Admin/CategoryManagement.php
```php
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
}```

## File: app/Livewire/Admin/DirectoryManagement.php
```php
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
```

## File: app/Livewire/Admin/EmployeeManagement.php
```php
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
}```

## File: app/Livewire/Admin/PartManagement.php
```php
<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Part;
use Livewire\Component;
use Livewire\WithPagination;

class PartManagement extends Component
{
    use WithPagination;

    public $name;

    public $sku;

    public $category_id;

    public $brand_id;

    public $quantity;

    public $description;

    public $editingPartId;

    public $showPartModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'required|exists:brands,id',
        'quantity' => 'required|integer|min:0',
    ];

    public function openPartModal()
    {
        $this->showPartModal = true;
    }

    public function generateSku()
    {
        $this->sku = 'SKU-'.strtoupper(bin2hex(random_bytes(4)));
    }

    public function closePartModal()
    {
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
```

## File: app/Livewire/Admin/ResourceManagement.php
```php
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
```

## File: app/Livewire/Admin/UserManagement.php
```php
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
        $this->phone = $user->phone;
        $this->cabinet = $user->cabinet;
        $this->selected_roles = $user->roles->pluck('name')->toArray();
        $this->openModal();
    }

    public function save() {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->editingUserId ?? 'NULL'),
            'selected_roles' => 'required|array|min:1',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'cabinet' => 'nullable|string|max:255',
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
}```

## File: app/Livewire/CreateTicket.php
```php
<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Instruction;
use App\Models\Ticket;
use Livewire\Component;

class CreateTicket extends Component
{
    public $categories;

    public $category_id;

    public $instructions = [];

    public $title;

    public $description;

    public $priority = 'normal';

    public $contact_name;

    public $contact_phone;

    public $contact_email;

    public $asset_id;

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatedCategoryId($value)
    {
        if (! $value) {
            $this->instructions = [];

            return;
        }

        // Берём ВСЕ клиентские инструкции (без PDF)
        $this->instructions = Instruction::where('category_id', $value)
            ->whereNull('pdf_path')
            ->get();
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

        $assignedTo = $category->technicians()
            ->withCount(['assignedTickets' => fn ($q) => $q->whereIn('status', ['new', 'in_progress'])])
            ->orderBy('assigned_tickets_count', 'asc')
            ->first()?->id;

        $dueDate = match ($this->priority) {
            'high' => now()->addHours(2),
            'low' => now()->addHours(48),
            default => now()->addHours(24),
        };

        $ticket = Ticket::create([
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
            'due_date' => $dueDate,
            'asset_id' => $this->asset_id,
        ]);

        session()->flash('message', 'Заявка создана! Ссылка для отслеживания: '.route('track', $ticket->uuid));

        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.create-ticket', [
            'assets' => Asset::where('user_id', auth()->id())->orWhereNull('user_id')->get(),
        ])->layout('layouts.app');
    }
}
```

## File: app/Livewire/DirectoryTable.php
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Footer;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class DirectoryTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'directory-table';

    public function setUp(): array
    {
        return [
            (new Exportable('export'))
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            (new Header())->showSearchInput(),
            (new Footer())
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with('department');
    }

    public function relationSearch(): array
    {
        return [
            'department' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('department_name', fn (User $model) => optional($model->department)->name)
            ->add('position')
            ->add('phone')
            ->add('cabinet');
    }

    public function columns(): array
    {
        return [
            Column::make('ФИО', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Отдел', 'department_name'),

            Column::make('Должность', 'position')
                ->sortable()
                ->searchable(),

            Column::make('Телефон', 'phone')
                ->sortable()
                ->searchable(),

            Column::make('Кабинет', 'cabinet')
                ->sortable()
                ->searchable(),
        ];
    }
}
```

## File: app/Livewire/ItDashboard.php
```php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Component;

class ItDashboard extends Component
{
    public $filterAssignee = '';

    public $filterCategory = '';

    public $technicians = [];

    public $categories = [];

    public function mount()
    {
        if (auth()->user()->hasRole('admin')) {
            $this->technicians = User::role(['it_support', 'admin'])->get();
            $this->categories = Category::all();
        }
    }

    public function render()
    {
        $query = Ticket::query();

        // Если не администратор, показываем только свои заявки
        if (! auth()->user()->hasRole('admin')) {
            $query->where('assigned_to', auth()->id());
        } else {
            if ($this->filterAssignee) {
                $query->where('assigned_to', $this->filterAssignee);
            }
            if ($this->filterCategory) {
                $query->where('category_id', $this->filterCategory);
            }
        }

        return view('livewire.it-dashboard', [
            'newTickets' => $query->clone()->where('status', 'new')->latest()->get(),
            'inProgressTickets' => $query->clone()->where('status', 'in_progress')->latest()->get(),
            'resolvedTickets' => $query->clone()->where('status', 'resolved')->latest()->get(),
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/KnowledgeBase.php
```php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class KnowledgeBase extends Component
{
    public $search = '';

    public $selectedCategory = '';

    public function render()
    {
        $instructions = Instruction::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->with('category')
            ->get();

        return view('livewire.knowledge-base', [
            'instructions' => $instructions,
            'categories' => Category::all(),
        ])->layout('layouts.app');
    }
}
```

## File: app/Livewire/ManageInstructions.php
```php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class ManageInstructions extends Component
{
    public $title;

    public $category_id;

    public $steps = [''];

    public $editingId;

    public $showModal = false;

    public function addStep()
    {
        $this->steps[] = '';
    }

    public function removeStep($index)
    {
        unset($this->steps[$index]);
        $this->steps = array_values($this->steps);
    }

    public function openModal()
    {
        $this->reset(['title', 'category_id', 'steps', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['title', 'category_id', 'steps', 'editingId']);
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'steps' => 'required|array']);
        Instruction::updateOrCreate(['id' => $this->editingId], [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'steps' => $this->steps,
        ]);
        $this->closeModal();
    }

    public function edit($id)
    {
        $instruction = Instruction::findOrFail($id);
        $this->editingId = $instruction->id;
        $this->title = $instruction->title;
        $this->category_id = $instruction->category_id;
        $this->steps = $instruction->steps ?? [''];
        $this->showModal = true;
    }

    public function delete($id)
    {
        Instruction::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.manage-instructions', [
            'instructions' => Instruction::whereNull('pdf_path')->with('category')->get(),
            'categories' => Category::all(),
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/NotificationBell.php
```php
<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead($id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->unreadNotifications,
        ]);
    }
}
```

## File: app/Livewire/PdfInstructions.php
```php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PdfInstructions extends Component
{
    use WithFileUploads;

    public $title;

    public $category_id;

    public $pdf;

    public $showModal = false;

    public $editingId;

    public $categories = [];

    public $instructions = [];

    public function mount()
    {
        $this->categories = Category::all();
        $this->loadInstructions();
    }

    public function loadInstructions()
    {
        $this->instructions = Instruction::whereNotNull('pdf_path')->get();
    }

    public function openModal()
    {
        $this->reset(['title', 'category_id', 'pdf', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['title', 'category_id', 'pdf', 'editingId']);
        $this->showModal = false;
    }

    public function edit($id)
    {
        $instruction = Instruction::findOrFail($id);
        $this->editingId = $instruction->id;
        $this->title = $instruction->title;
        $this->category_id = $instruction->category_id;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $instruction = Instruction::findOrFail($id);
        if ($instruction->pdf_path) {
            Storage::disk('public')->delete($instruction->pdf_path);
        }
        $instruction->delete();
        $this->loadInstructions();
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ];

        if (! $this->editingId || $this->pdf) {
            $rules['pdf'] = 'required|file|mimes:pdf|max:10240';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'category_id' => $this->category_id,
        ];

        if ($this->pdf) {
            // Удаляем старый файл при замене
            if ($this->editingId) {
                $oldInst = Instruction::find($this->editingId);
                if ($oldInst && $oldInst->pdf_path) {
                    Storage::disk('public')->delete($oldInst->pdf_path);
                }
            }
            $data['pdf_path'] = $this->pdf->store('instructions', 'public');
            $data['steps'] = null;
        }

        Instruction::updateOrCreate(['id' => $this->editingId], $data);

        $this->closeModal();
        $this->loadInstructions();

        session()->flash('message', 'PDF инструкция добавлена!');
    }

    public function render()
    {
        return view('livewire.pdf-instructions')
            ->layout('layouts.admin');
    }
}
```

## File: app/Livewire/TicketDetail.php
```php
<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Instruction;
use App\Models\Part;
use App\Models\Ticket;
use App\Notifications\NewCommentNotification;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Support\Str;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;

    public $body;

    public $instruction; // текстовая (если есть)

    public $pdfInstruction; // 🔥 PDF

    public $showInstruction = false;

    public $part_id;

    public $asset_id;

    public $rating;

    public $feedback_comment;

    public function submitFeedback()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string',
        ]);

        $this->ticket->update([
            'rating' => $this->rating,
            'feedback_comment' => $this->feedback_comment,
        ]);

        session()->flash('message', 'Спасибо за вашу оценку!');
    }

    public function generateUuid()
    {
        if (! $this->ticket->uuid) {
            $this->ticket->update(['uuid' => (string) Str::uuid()]);
            session()->flash('message', 'Публичный ключ успешно сгенерирован!');
        }
    }

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->asset_id = $ticket->asset_id;

        // ТЕКСТОВАЯ инструкция (если нужна)
        $this->instruction = Instruction::where('category_id', $ticket->category_id)
            ->whereNull('pdf_path')
            ->first();

        // 🔥 PDF инструкция
        $this->pdfInstruction = Instruction::where('category_id', $ticket->category_id)
            ->whereNotNull('pdf_path')
            ->first();
    }

    public function toggleInstruction()
    {
        $this->showInstruction = ! $this->showInstruction;
    }

    public function getAvailablePartsProperty()
    {
        return Part::where('category_id', $this->ticket->category_id)->get();
    }

    public function attachPart()
    {
        if (! $this->part_id) {
            return;
        }

        $part = Part::findOrFail($this->part_id);

        if ($part->quantity > 0) {
            $part->decrement('quantity');

            $this->ticket->comments()->create([
                'user_id' => auth()->id(),
                'body' => 'Использована запчасть: '.$part->name,
            ]);

            session()->flash('message', 'Запчасть успешно списана!');
        } else {
            session()->flash('error', 'Запчасть закончилась!');
        }
    }

    public function updateStatus($status)
    {
        $this->ticket->update(['status' => $status]);

        // Уведомляем автора заявки
        $this->ticket->user->notify(new TicketStatusUpdated($this->ticket, $status));

        session()->flash('message', 'Статус обновлен!');
    }

    public function addComment()
    {
        if (! $this->body) {
            return;
        }

        $comment = $this->ticket->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->body,
        ]);

        // Уведомляем техзадание/автора (кто не является автором комментария)
        $recipients = collect([$this->ticket->user, $this->ticket->assignee])
            ->filter()
            ->unique('id')
            ->reject(fn ($u) => $u->id === auth()->id());

        foreach ($recipients as $recipient) {
            $recipient->notify(new NewCommentNotification($comment));
        }

        $this->reset('body');
    }

    public function updateAsset()
    {
        $this->ticket->update(['asset_id' => $this->asset_id]);
        session()->flash('message', 'Оборудование привязано к заявке!');
    }

    public function render()
    {
        return view('livewire.ticket-detail', [
            'parts' => $this->availableParts,
            'assets' => Asset::all(),
        ])->layout('layouts.admin');
    }
}
```

## File: app/Livewire/TicketTracking.php
```php
<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class TicketTracking extends Component
{
    public $ticket;

    public $rating;

    public $feedback_comment;

    public function mount($uuid)
    {
        $this->ticket = Ticket::where('uuid', $uuid)
            ->with(['category', 'assignee', 'activities', 'activities.causer'])
            ->firstOrFail();
    }

    public function submitFeedback()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string',
        ]);

        $this->ticket->update([
            'rating' => $this->rating,
            'feedback_comment' => $this->feedback_comment,
        ]);

        session()->flash('message', 'Спасибо за вашу оценку!');
    }

    public function render()
    {
        return view('livewire.ticket-tracking')
            ->layout('layouts.app');
    }
}
```

## File: app/Notifications/NewCommentNotification.php
```php
<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->comment->ticket_id,
            'user_name' => $this->comment->user->name,
            'message' => "Новый комментарий в заявке #{$this->comment->ticket_id} от {$this->comment->user->name}",
        ];
    }
}
```

## File: app/Notifications/TicketStatusUpdated.php
```php
<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public string $status) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'status' => $this->status,
            'message' => "Статус заявки #{$this->ticket->id} изменен на: ".$this->getStatusLabel(),
        ];
    }

    protected function getStatusLabel(): string
    {
        return match ($this->status) {
            'new' => 'Новая',
            'in_progress' => 'В работе',
            'resolved' => 'Готово',
            default => $this->status
        };
    }
}
```

## File: database/migrations/0001_01_01_000000_create_users_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

## File: database/migrations/0001_01_01_000001_create_cache_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
```

## File: database/migrations/0001_01_01_000002_create_jobs_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
```

## File: database/migrations/2026_04_23_095347_create_permission_tables.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->id(); // permission id
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->id(); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
```

## File: database/migrations/2026_04_23_095427_create_departments_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
```

## File: database/migrations/2026_04_23_095434_create_categories_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('default_assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

## File: database/migrations/2026_04_23_095443_create_instructions_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
```

## File: database/migrations/2026_04_23_095454_create_tickets_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('new'); // new, in_progress, resolved
            $table->string('priority')->default('normal'); // normal, high
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
```

## File: database/migrations/2026_04_23_095459_create_comments_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
```

## File: database/migrations/2026_04_23_095540_add_fields_to_users_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('cabinet')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['position', 'phone', 'cabinet']);
        });
    }
};
```

## File: database/migrations/2026_04_23_103925_create_government_resources_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('government_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('government_resources');
    }
};
```

## File: database/migrations/2026_04_23_103925_update_instructions_table_to_steps.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->json('steps')->nullable()->after('title');
            $table->dropColumn('content');
        });
    }

    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->text('content')->nullable();
            $table->dropColumn('steps');
        });
    }
};
```

## File: database/migrations/2026_04_23_122834_create_brands_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
```

## File: database/migrations/2026_04_23_123020_create_parts_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
```

## File: database/migrations/2026_04_23_190813_create_category_user_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_user');
    }
};
```

## File: database/migrations/2026_04_23_204738_add_pdf_path_and_contacts_to_tables.php
```php
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
};```

## File: database/migrations/2026_04_24_073958_create_activity_log_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }
};
```

## File: database/migrations/2026_04_24_074007_create_assets_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('type')->nullable(); // Printer, PC, Monitor
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Vladelets
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('status')->default('active'); // active, maintenance, retired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
```

## File: database/migrations/2026_04_24_074024_add_advanced_fields_to_tickets_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique();
            $table->dateTime('due_date')->nullable();
            $table->integer('rating')->nullable();
            $table->text('feedback_comment')->nullable();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn(['uuid', 'due_date', 'rating', 'feedback_comment', 'asset_id']);
        });
    }
};
```

## File: database/migrations/2026_04_24_083206_create_notifications_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

## File: database/seeders/DatabaseSeeder.php
```php
<?php

namespace Database\Seeders;

use App\Models\{Category, Department, Instruction, Part, Brand, GovernmentResource, User, Ticket, Comment, Asset};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Очистка (необязательно, но полезно для "чистого" демо)
        // Schema::disableForeignKeyConstraints();
        // User::truncate(); ...
        // Schema::enableForeignKeyConstraints();

        // 2. Роли
        $roles = ['admin', 'it_support', 'manager', 'employee'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 3. Отделы
        $departments = [
            'ИТ-отдел' => 'информационных технологий',
            'Бухгалтерия' => 'финансового обеспечения',
            'Отдел кадров' => 'управления персоналом',
            'Юридический отдел' => 'правового обеспечения',
            'Приемная' => 'административный отдел',
        ];

        $deptModels = [];
        foreach ($departments as $name => $desc) {
            $deptModels[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // 4. Пользователи (Админы и Техники)
        $admins = [
            ['name' => 'Никита Администратор', 'email' => 'admin@test.com'],
            ['name' => 'Главный Админ', 'email' => 'root@test.com'],
        ];

        foreach ($admins as $a) {
            $user = User::updateOrCreate(['email' => $a['email']], [
                'name' => $a['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels['ИТ-отдел']->id,
                'position' => 'Системный администратор',
            ]);
            $user->assignRole('admin');
        }

        $technicians = [
            ['name' => 'Алексей Техников', 'email' => 'tech1@test.com'],
            ['name' => 'Мария Поддержкова', 'email' => 'tech2@test.com'],
            ['name' => 'Дмитрий Сервисов', 'email' => 'tech3@test.com'],
        ];

        $techModels = [];
        foreach ($technicians as $t) {
            $user = User::updateOrCreate(['email' => $t['email']], [
                'name' => $t['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels['ИТ-отдел']->id,
                'position' => 'Специалист техподдержки',
            ]);
            $user->assignRole('it_support');
            $techModels[] = $user;
        }

        // 5. Обычные сотрудники (Клиенты)
        $employees = [
            ['name' => 'Иван Иванов', 'email' => 'user1@test.com', 'dept' => 'Бухгалтерия'],
            ['name' => 'Ольга Петрова', 'email' => 'user2@test.com', 'dept' => 'Отдел кадров'],
            ['name' => 'Сергей Сидоров', 'email' => 'user3@test.com', 'dept' => 'Юридический отдел'],
            ['name' => 'Анна Смирнова', 'email' => 'user4@test.com', 'dept' => 'Бухгалтерия'],
        ];

        $employeeModels = [];
        foreach ($employees as $e) {
            $user = User::updateOrCreate(['email' => $e['email']], [
                'name' => $e['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels[$e['dept']]->id,
                'position' => 'Ведущий специалист',
                'phone' => '+7 (900) ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99),
            ]);
            $user->assignRole('employee');
            $employeeModels[] = $user;
        }

        // 6. Бренды и Оборудование (Assets)
        $brands = ['HP', 'Dell', 'Lenovo', 'Cisco', 'Kyocera', 'APC'];
        $brandModels = [];
        foreach ($brands as $b) {
            $brandModels[] = Brand::updateOrCreate(['name' => $b], ['slug' => Str::slug($b)]);
        }

        $assetTypes = ['pc', 'printer', 'monitor', 'network'];
        for ($i = 1; $i <= 20; $i++) {
            Asset::updateOrCreate(
                ['serial_number' => 'SN-' . strtoupper(Str::random(8))],
                [
                    'name' => $brands[array_rand($brands)] . ' ' . ['Workstation', 'ProBook', 'LaserJet', 'OptiPlex'][rand(0, 3)] . ' ' . $i,
                    'type' => $assetTypes[array_rand($assetTypes)],
                    'department_id' => collect($deptModels)->random()->id,
                    'user_id' => (rand(0, 1) ? collect($employeeModels)->random()->id : null),
                    'status' => ['active', 'active', 'active', 'repair'][rand(0, 3)],
                ]
            );
        }

        // 7. Категории и Инструкции
        $categories = [
            'Проблемы с печатью' => ['default_tech' => 0, 'steps' => ['Проверить бумагу', 'Перезагрузить принтер', 'Очистить очередь печати']],
            'Доступ к почте' => ['default_tech' => 1, 'steps' => ['Проверить интернет', 'Ввести пароль заново', 'Сбросить кэш Outlook']],
            'Настройка VPN' => ['default_tech' => 2, 'steps' => ['Запустить Cisco AnyConnect', 'Ввести адрес сервера', 'Подключить токен']],
            'Замена картриджа' => ['default_tech' => 0, 'steps' => ['Достать старый картридж', 'Встряхнуть новый', 'Установить до щелчка']],
            'Не работает интернет' => ['default_tech' => 2, 'steps' => ['Проверить кабель', 'Перезагрузить роутер', 'Связаться с ИТ']],
        ];

        $categoryModels = [];
        foreach ($categories as $name => $data) {
            $cat = Category::updateOrCreate(['name' => $name], [
                'default_assignee_id' => $techModels[$data['default_tech']]->id
            ]);
            $cat->technicians()->sync([$techModels[$data['default_tech']]->id]);
            
            Instruction::updateOrCreate(['category_id' => $cat->id, 'title' => 'Как решить самостоятельно: ' . $name], [
                'steps' => $data['steps']
            ]);
            
            $categoryModels[] = $cat;

            // Запчасти для категории
            Part::updateOrCreate(['name' => 'Запчасть для ' . $name, 'category_id' => $cat->id], [
                'sku' => strtoupper(Str::random(6)),
                'brand_id' => collect($brandModels)->random()->id,
                'quantity' => rand(10, 100),
                'description' => 'Универсальная запчасть для категории ' . $name
            ]);
        }

        // 8. Заявки (Tickets)
        $statuses = ['new', 'new', 'in_progress', 'in_progress', 'resolved', 'resolved', 'resolved'];
        $priorities = ['low', 'normal', 'normal', 'high'];

        for ($i = 1; $i <= 40; $i++) {
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            $user = collect($employeeModels)->random();
            $cat = collect($categoryModels)->random();
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            
            $dueDate = (clone $createdAt)->addHours($priority === 'high' ? 2 : ($priority === 'low' ? 48 : 24));

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'category_id' => $cat->id,
                'title' => 'Проблема #' . $i . ': ' . $cat->name,
                'description' => 'У меня возникла проблема в категории ' . $cat->name . '. Пожалуйста, помогите разобраться. Номер кабинета: ' . rand(100, 500),
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => ($status !== 'new' ? $techModels[array_rand($techModels)]->id : null),
                'contact_name' => $user->name,
                'contact_phone' => $user->phone,
                'contact_email' => $user->email,
                'uuid' => (string) Str::uuid(),
                'created_at' => $createdAt,
                'due_date' => $dueDate,
                'asset_id' => Asset::inRandomOrder()->first()?->id,
                'rating' => ($status === 'resolved' ? (rand(1, 10) > 3 ? rand(4, 5) : rand(1, 3)) : null),
                'feedback_comment' => ($status === 'resolved' ? 'Спасибо за быструю работу!' : null),
            ]);

            // 9. Комментарии
            if ($status !== 'new') {
                Comment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->assigned_to,
                    'body' => 'Принял заявку в работу. Скоро буду.',
                    'created_at' => (clone $createdAt)->addMinutes(15),
                ]);

                if ($status === 'resolved') {
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $ticket->assigned_to,
                        'body' => 'Проблема устранена. Все работает.',
                        'created_at' => (clone $createdAt)->addHours(rand(1, 5)),
                    ]);
                }
            }
        }

        GovernmentResource::updateOrCreate(['name' => 'Госуслуги'], ['url' => 'https://www.gosuslugi.ru']);
        GovernmentResource::updateOrCreate(['name' => 'Минцифры РФ'], ['url' => 'https://digital.gov.ru']);
    }
}```

## File: resources/views/components/admin/⚡admin-dashboard.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- Walk as if you are kissing the Earth with your feet. - Thich Nhat Hanh --}}
</div>```

## File: resources/views/components/admin/⚡category-management.blade.php
```php
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление категориями</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg shadow-sm border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingCategoryId ? 'Редактировать' : 'Добавить' }} категорию</h3>
            <form wire:submit.prevent="createCategory" @submit.prevent="editingCategoryId ? updateCategory() : createCategory()" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Название категории</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Напр: Замена картриджа">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all shadow-md active:scale-95">
                        {{ $editingCategoryId ? 'ОБНОВИТЬ' : 'СОЗДАТЬ' }}
                    </button>
                    @if($editingCategoryId)
                        <button type="button" wire:click="$set('editingCategoryId', null)" class="bg-slate-100 text-slate-500 font-bold px-6 rounded-lg hover:bg-slate-200 transition-colors">ОТМЕНА</button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Список категорий -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($categories as $category)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group">
                    <div class="p-6 flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg">{{ $category->name }}</h4>
                            <div class="mt-4 space-y-2 text-sm text-slate-600">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                    <span>Запчастей: {{ $category->parts_count }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">menu_book</span>
                                    <span>Инструкций: {{ $category->instructions_count }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined">support_agent</span>
                                    <span>Заявок: {{ $category->tickets_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="editCategory({{ $category->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button onclick="return confirm('Удалить эту категорию? Это также удалит все связанные запчасти, инструкции и заявки. Продолжить?')" wire:click="deleteCategory({{ $category->id }})" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div>
    {{-- We must ship. - Taylor Otwell --}}
</div>```

## File: resources/views/components/admin/⚡part-management.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- Simplicity is the consequence of refined emotions. - Jean D'Alembert --}}
</div>```

## File: resources/views/components/⚡create-ticket.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius --}}
</div>```

## File: resources/views/components/⚡it-dashboard.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- Simplicity is the ultimate sophistication. - Leonardo da Vinci --}}
</div>```

## File: resources/views/components/⚡manage-instructions.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- Smile, breathe, and go slowly. - Thich Nhat Hanh --}}
</div>```

## File: resources/views/components/⚡ticket-detail.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- Waste no more time arguing what a good man should be, be one. - Marcus Aurelius --}}
</div>```

## File: resources/views/components/⚡ticket-tracking.blade.php
```php
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    {{-- No surplus words or unnecessary actions. - Marcus Aurelius --}}
</div>```

## File: resources/views/directory.blade.php
```php
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-10">
            <h2 class="text-4xl font-extrabold text-emerald-900 uppercase tracking-tighter">Телефонный справочник</h2>
            <p class="text-slate-500 mt-2 font-medium">Контактная информация сотрудников и подразделений комитета</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-2 overflow-hidden">
            <livewire:directory-table />
        </div>
    </div>
@endsection
```

## File: resources/views/home.blade.php
```php
@extends('layouts.app')

@section('content')
    <section class="relative w-full min-h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACKqpnlTPoNj3U6UR33X72XE9euFYb4CenyvHWb-FRgs41Zo1I_7epi96KZmHhoL33QnSU2v7Po0iaWO0Nn7ckGPA6-cnmHpEFFJkc_p8dCwYgcIUgdxZ9XLifAealYwFSs0D9ueSzjlYxkEQ45xh7He0WMuGOk33oGB2ClsZzV5hGjlzZWaPpuelt68NKl2Z66KTJ-dhERLWBWC7352rpVqG00uVah2SDWzY8196fEhjha2BcqSjC57NHpHmPTq0fBJVixdOfUx5D" />
            <div class="absolute inset-0 bg-emerald-950/60 mix-blend-multiply"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 drop-shadow-lg leading-tight">
                Комитет сельского хозяйства <br> <span class="text-white font-medium text-3xl md:text-5xl">Волгоградской области</span>
            </h1>
            <p class="text-xl text-white mb-10 max-w-3xl mx-auto font-medium opacity-90">
                Корпоративный портал для сотрудников. Единый справочник и система подачи заявок в службу технической поддержки.
            </p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="/support" class="bg-emerald-500 text-white hover:bg-emerald-400 px-10 py-5 rounded-xl font-bold transition-all shadow-xl active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined">support_agent</span>
                    Подать заявку
                </a>
                <a href="/directory" class="bg-white/10 backdrop-blur-md text-white border border-white/30 hover:bg-white/20 px-10 py-5 rounded-xl font-bold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">contact_phone</span>
                    Справочник
                </a>
            </div>
        </div>
    </section>

    <!-- Гос ресурсы -->
    <section class="bg-white border-y border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-3 mb-8">
                <span class="material-symbols-outlined text-emerald-700">account_balance</span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Государственные ресурсы</h2>
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($resources as $res)
                    <a href="{{ $res->url }}" target="_blank" class="px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-emerald-50/10 hover:border-emerald-500 transition-all font-bold text-slate-600 flex items-center gap-2 group">
                        {{ $res->name }}
                        <span class="material-symbols-outlined text-sm opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">hub</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Техподдержка</h3>
                <p class="text-slate-500 leading-relaxed">Быстрое решение технических проблем и консультации специалистов ИТ-отдела.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group border-b-4 border-b-emerald-500">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">style</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Справочник</h3>
                <p class="text-slate-500 leading-relaxed">Контакты всех сотрудников и отделов комитета. Удобный поиск по должностям и кабинетам.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-10 hover:shadow-2xl hover:-translate-y-2 transition-all group">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-white flex items-center justify-center mb-6 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">menu_book</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">База знаний</h3>
                <p class="text-slate-500 leading-relaxed">Инструкции и руководства для самостоятельного решения типовых задач без ожидания мастера.</p>
            </div>
        </div>
    </section>
@endsection
```

## File: resources/views/layouts/admin.blade.php
```php
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
                <a href="/admin/assets" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/assets*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">inventory_2</span> Оборудование
                </a>
                <a href="/admin/analytics" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/analytics*') ? 'bg-emerald-800 text-white' : 'text-emerald-100 hover:bg-emerald-900' }}">
                    <span class="material-symbols-outlined">analytics</span> Аналитика
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
                    <span class="material-symbols-outlined">picture_as_pdf</span> Инструкции (сотрудники)
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
                <div class="flex items-center gap-6">
                    <div class="bg-emerald-950 rounded-xl p-1 shadow-inner">
                        <livewire:notification-bell />
                    </div>
                    <form method="POST" action="/logout">@csrf <button class="text-slate-500 font-bold text-xs uppercase hover:text-red-500 transition-colors">Выход</button></form>
                </div>
            </header>
            <main class="flex-grow overflow-y-auto p-8">
                {{ $slot ?? '' }} @yield('content')
            </main>
        </div>
    </div>
</body>
</html>```

## File: resources/views/layouts/app.blade.php
```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'ГКУ ВО "МАЦ"' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background text-on-background font-body-lg antialiased">
        <nav class="bg-emerald-950 fixed top-0 w-full z-50 h-20 border-b border-emerald-900 font-['Public_Sans'] shadow-lg">
            <div class="flex justify-between items-center max-w-7xl mx-auto px-6 lg:px-24 w-full h-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-900 font-bold">agriculture</span>
                    </div>
                    <div class="text-xl font-extrabold text-white uppercase tracking-tighter">
                        ГКУ ВО "МАЦ"
                    </div>
                </div>
                <div class="hidden md:flex space-x-2 items-center">
                     <a href="/" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('/') ? 'bg-emerald-900 text-white font-bold' : '' }}">Главная</a>
                     <a href="/directory" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('directory') ? 'bg-emerald-900 text-white font-bold' : '' }}">Справочник</a>
                     <a href="/knowledge-base" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('knowledge-base') ? 'bg-emerald-900 text-white font-bold' : '' }}">База знаний</a>
                     <a href="/support" class="px-4 py-2 rounded-lg text-white hover:bg-emerald-900/50 transition-all {{ request()->is('support') ? 'bg-emerald-900 text-white font-bold' : '' }}">Поддержка</a>
                 </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center gap-6">
                            <livewire:notification-bell />
                            <div class="flex items-center gap-4">
@hasanyrole('admin|it_support|manager')
    @php
        $dashboardUrl = '#';
        if (auth()->user()->hasRole('admin')) {
            $dashboardUrl = route('admin.users');
        } elseif (auth()->user()->hasRole('it_support')) {
            $dashboardUrl = route('it-dashboard');
        } elseif (auth()->user()->hasRole('manager')) {
            $dashboardUrl = route('admin.directory');
        }
    @endphp
    <a href="{{ $dashboardUrl }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-bold border border-white/20 transition-all">Панель управления</a>
@endhasanyrole
                            <div class="flex flex-col items-end">
                                <span class="text-sm text-white font-bold">{{ auth()->user()->name }}</span>
                                <form method="POST" action="/logout" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-emerald-300 hover:text-white uppercase font-bold tracking-widest transition-colors">Выход</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed px-6 py-2 rounded-lg font-bold transition-all shadow-md active:scale-95">Вход</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="pt-20 min-h-screen">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <footer class="bg-slate-900 text-white w-full py-16 mt-20 border-t border-emerald-900 font-['Public_Sans']">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 max-w-7xl mx-auto px-6 lg:px-24">
                <div class="md:col-span-4">
                    <div class="text-lg font-bold text-emerald-400 mb-4">ГКУ ВО "МАЦ"</div>
                    <p class="text-slate-400 mb-6 max-w-xs text-sm">
                        Институциональная стабильность и современный прогресс. Обеспечение роста и безопасности сельскохозяйственного сектора Волгоградской области.
                    </p>
                    <div class="text-slate-500 text-xs uppercase font-bold tracking-widest">
                        © 2024 Все права защищены.
                    </div>
                </div>
                <div class="md:col-span-8 flex flex-wrap gap-8 justify-end">
                    <div class="flex flex-col gap-3">
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Противодействие коррупции</a>
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Открытые данные</a>
                        <a class="text-slate-400 hover:text-emerald-400 transition-all text-sm" href="#">Политика конфиденциальности</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
```

## File: resources/views/livewire/admin/analytics-dashboard.blade.php
```php
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Аналитика и отчетность</h2>
        <p class="text-slate-500 font-medium">Обзор эффективности работы службы технической поддержки.</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">confirmation_number</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Всего заявок</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Решено</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['resolved'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">pending_actions</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">В ожидании</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['pending'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">star</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Средняя оценка</div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['avg_rating'] ?: '—' }} <span class="text-xs text-slate-300 font-medium">/ 5</span></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- PERFORMANCE BY TECH --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-extrabold text-emerald-900 uppercase tracking-tighter">Производительность специалистов</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Топ-5 по решенным заявкам</span>
            </div>
            <div class="p-8 space-y-6">
                @foreach($techPerformance as $tech)
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-900 text-white flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr($tech->name, 0, 2) }}
                                </div>
                                <div class="font-bold text-slate-800 text-sm">{{ $tech->name }}</div>
                            </div>
                            <div class="text-sm font-black text-emerald-600">{{ $tech->resolved_count }} заявок</div>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" 
                                 style="width: {{ $stats['resolved'] > 0 ? ($tech->resolved_count / $stats['resolved'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- STATUS DISTRIBUTION --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-extrabold text-emerald-900 uppercase tracking-tighter text-center">Распределение по статусам</h3>
            </div>
            <div class="p-8 flex-grow flex flex-col justify-center space-y-4">
                @foreach($statusDistribution as $dist)
                    @php
                        $color = match($dist->status) {
                            'new' => 'blue',
                            'in_progress' => 'amber',
                            'resolved' => 'emerald',
                            default => 'slate'
                        };
                        $label = match($dist->status) {
                            'new' => 'Новые',
                            'in_progress' => 'В работе',
                            'resolved' => 'Решено',
                            default => $dist->status
                        };
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-100 group hover:scale-[1.02] transition-transform">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-{{ $color }}-500"></div>
                            <span class="text-sm font-bold text-{{ $color }}-900 uppercase tracking-wide">{{ $label }}</span>
                        </div>
                        <span class="text-lg font-black text-{{ $color }}-700">{{ $dist->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

## File: resources/views/livewire/admin/asset-management.blade.php
```php
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Учет оборудования</h2>
            <p class="text-slate-500 font-medium">Управление инвентарем и техникой организации.</p>
        </div>
        
        <div class="flex gap-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Поиск по названию или S/N..." 
                       class="pl-10 pr-4 py-3 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 w-64 bg-white shadow-sm text-sm">
                <span class="material-symbols-outlined absolute left-3 top-3.5 text-slate-400 text-lg">search</span>
            </div>
            
            <button wire:click="openAssetModal" class="bg-emerald-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                НОВОЕ ОБОРУДОВАНИЕ
            </button>
        </div>
    </div>

    @if($showAssetModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-md bg-black/40 animate-in fade-in duration-300">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border border-slate-200">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-emerald-900 uppercase tracking-tighter">
                        {{ $editingAssetId ? 'Редактировать оборудование' : 'Новое оборудование' }}
                    </h3>
                    <button wire:click="closeAssetModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form wire:submit.prevent="save" class="p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Название устройства</label>
                            <input type="text" wire:model="name" placeholder="Напр. HP LaserJet Pro M404n" 
                                   class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                            @error('name') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Серийный номер (S/N)</label>
                            <input type="text" wire:model="serial_number" placeholder="CNB1J23456" 
                                   class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                            @error('serial_number') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Тип устройства</label>
                            <select wire:model="type" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Выберите тип</option>
                                <option value="pc">Компьютер / Ноутбук</option>
                                <option value="printer">Принтер / МФУ</option>
                                <option value="monitor">Монитор</option>
                                <option value="network">Сетевое оборудование</option>
                                <option value="other">Другое</option>
                            </select>
                            @error('type') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Статус</label>
                            <select wire:model="status" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50 font-bold">
                                <option value="active">🟢 В работе</option>
                                <option value="repair">🟡 В ремонте</option>
                                <option value="retired">🔴 Списано</option>
                            </select>
                            @error('status') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Отдел</label>
                            <select wire:model="department_id" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Все отделы</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Ответственное лицо</label>
                            <select wire:model="user_id" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                                <option value="">Не назначено</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeAssetModal" 
                                class="bg-slate-100 text-slate-500 font-bold py-3 px-6 rounded-xl hover:bg-slate-200 transition-all uppercase text-xs tracking-widest">
                            Отмена
                        </button>
                        <button type="submit" 
                                class="bg-emerald-900 text-white font-bold py-3 px-8 rounded-xl hover:bg-emerald-800 transition-all uppercase text-xs tracking-widest shadow-lg">
                            {{ $editingAssetId ? 'Обновить' : 'Сохранить' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Оборудование</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Тип</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Владелец / Отдел</th>
                        <th class="px-8 py-5 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Статус</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900 group-hover:text-emerald-900 transition-colors">{{ $asset->name }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-1">S/N: {{ $asset->serial_number ?? '—' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                    {{ $asset->type }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-sm">
                                <div class="font-bold text-slate-700">{{ optional($asset->user)->name ?? '—' }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-medium">{{ optional($asset->department)->name ?? 'Без отдела' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'repair' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'retired' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusNames = [
                                        'active' => 'В работе',
                                        'repair' => 'В ремонте',
                                        'retired' => 'Списано',
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-[10px] font-extrabold uppercase border {{ $statusClasses[$asset->status] ?? 'bg-slate-100' }}">
                                    {{ $statusNames[$asset->status] ?? $asset->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right space-x-3">
                                <button wire:click="edit({{ $asset->id }})" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                    <span class="material-symbols-outlined text-lg">edit_note</span>
                                </button>
                                <button wire:click="delete({{ $asset->id }})" wire:confirm="Вы уверены?" class="text-red-400 hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-medium">
                                <span class="material-symbols-outlined text-4xl mb-3 block">inventory_2</span>
                                Оборудование не найдено.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
            {{ $assets->links() }}
        </div>
    </div>
</div>
```

## File: resources/views/livewire/admin/brand-management.blade.php
```php
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Управление брендами</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Форма -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit lg:sticky lg:top-8">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingBrandId ? 'Редактировать' : 'Новый' }} бренд</h3>
            <form wire:submit.prevent="{{ $editingBrandId ? 'updateBrand' : 'createBrand' }}" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Название</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Slug</label>
                    <input type="text" wire:model="slug" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('slug') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Описание</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-300 focus:border-emerald-500"></textarea>
                    @error('description') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-grow bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                        {{ $editingBrandId ? 'ОБНОВИТЬ' : 'СОЗДАТЬ' }}
                    </button>
                    @if($editingBrandId)
                        <button type="button" wire:click="reset(['name', 'slug', 'description', 'editingBrandId'])" class="bg-slate-100 text-slate-500 font-bold px-4 rounded-lg">Отмена</button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Таблица -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Бренд</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Запчастей</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Действие</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($brands as $brand)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-emerald-900">{{ $brand->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $brand->slug }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $brand->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $brand->parts_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="editBrand({{ $brand->id }})" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase">Изменить</button>
                                    <button wire:click="deleteBrand({{ $brand->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Удалить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>```

## File: resources/views/livewire/admin/category-management.blade.php
```php
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
</div>```

## File: resources/views/livewire/admin/directory-management.blade.php
```php
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
```

## File: resources/views/livewire/admin/employee-management.blade.php
```php
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
                    <input type="text" wire:model.live="cabinet" placeholder="Кабинет" class="w-full rounded-lg border-slate-300">
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
</div>```

## File: resources/views/livewire/admin/part-management.blade.php
```php
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
                    
                    <div class="flex gap-2">
                        <input type="text" wire:model.live="sku" placeholder="Артикул (SKU)" class="flex-1 rounded-lg border-slate-300">
                        <button type="button" wire:click="generateSku" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-300 transition-colors">
                            Генерировать
                        </button>
                    </div>
                    
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
</div>```

## File: resources/views/livewire/admin/resource-management.blade.php
```php
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 uppercase tracking-wide">Государственные ресурсы</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit">
            <h3 class="font-bold text-emerald-900 mb-6 uppercase tracking-wider text-sm">{{ $editingResourceId ? 'Редактировать' : 'Добавить' }} ресурс</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Название</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-emerald-500">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Ссылка (URL)</label>
                    <input type="url" wire:model="url" class="w-full rounded-lg border-slate-300 focus:border-emerald-500" placeholder="https://...">
                    @error('url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all">
                    {{ $editingResourceId ? 'ОБНОВИТЬ' : 'СОХРАНИТЬ' }}
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Ресурс</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($resources as $res)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-emerald-900">{{ $res->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $res->url }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $res->id }})" class="text-blue-600 hover:text-blue-900 mr-4 font-bold text-xs uppercase">Изменить</button>
                                    <button onclick="confirm('Удалить?') || event.stopImmediatePropagation()" wire:click="delete({{ $res->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Удалить</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

## File: resources/views/livewire/admin/user-management.blade.php
```php
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
                    <select wire:model.live="department_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->name }}</option> @endforeach
                    </select>
                    <input type="text" wire:model.live="position" placeholder="Должность" class="w-full rounded-lg border-slate-300">
                    <input type="text" wire:model.live="phone" placeholder="Телефон" class="w-full rounded-lg border-slate-300">
                    <input type="text" wire:model.live="cabinet" placeholder="Кабинет" class="w-full rounded-lg border-slate-300">
                    
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
</div>```

## File: resources/views/livewire/create-ticket.blade.php
```php
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

            <select wire:model="asset_id" class="w-full rounded-xl border-slate-300 focus:border-emerald-500">
                <option value="">Связать с оборудованием (необязательно)</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->serial_number }})</option>
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
                @endforeach
            </div>
        @endif

        <button type="submit"
            class="w-full bg-emerald-900 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition-all active:scale-95 shadow-lg">
            ОТПРАВИТЬ ЗАЯВКУ
        </button>
    </form>
</div>```

## File: resources/views/livewire/it-dashboard.blade.php
```php
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-emerald-900 uppercase tracking-wide">Кабинет ИТ-специалиста</h2>
            <p class="text-slate-600">Управление заявками и техническая поддержка сотрудников.</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-4 items-center">
            @role('admin')
                <select wire:model.live="filterCategory" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 py-2">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterAssignee" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 py-2">
                    <option value="">Все сотрудники</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
            @endrole

            <div class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm shadow-sm flex items-center">
                <span class="text-slate-500 mr-2">Всего заявок:</span>
                <span class="font-bold text-emerald-900">{{ \App\Models\Ticket::count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Новые -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">Новые ({{ $newTickets->count() }})</h3>
            </div>
            
            <div class="space-y-4">
                @foreach($newTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                            <div class="flex flex-col gap-1 items-end">
                                @if($ticket->priority == 'high')
                                    <span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase">Срочно</span>
                                @endif
                                @if($ticket->due_date && $ticket->due_date < now())
                                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase animate-pulse">Просрочено</span>
                                @elseif($ticket->due_date && $ticket->due_date < now()->addHours(2))
                                    <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase">Осталось мало времени</span>
                                @endif
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700">{{ $ticket->title }}</h4>
                        <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $ticket->description }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- В работе -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">В работе ({{ $inProgressTickets->count() }})</h3>
            </div>

            <div class="space-y-4">
                @foreach($inProgressTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                            <div class="flex flex-col gap-1 items-end">
                                @if($ticket->priority == 'high')
                                    <span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase">Срочно</span>
                                @endif
                                @if($ticket->due_date && $ticket->due_date < now())
                                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase animate-pulse">Просрочено</span>
                                @elseif($ticket->due_date && $ticket->due_date < now()->addHours(2))
                                    <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase">Осталось мало времени</span>
                                @endif
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700">{{ $ticket->title }}</h4>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center text-[10px] font-bold text-amber-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Готово -->
        <div class="bg-slate-100 rounded-xl p-4 min-h-[600px]">
            <div class="flex items-center gap-2 mb-4 px-2">
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-sm">Готово ({{ $resolvedTickets->count() }})</h3>
            </div>

            <div class="space-y-4 opacity-75">
                @foreach($resolvedTickets as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700 line-through">{{ $ticket->title }}</h4>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-700">
                                    {{ mb_substr($ticket->contact_name ?? $ticket->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] text-slate-600">{{ $ticket->contact_name ?? $ticket->user->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

## File: resources/views/livewire/knowledge-base.blade.php
```php
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-extrabold text-emerald-900 uppercase tracking-tighter mb-4">База знаний</h2>
        <p class="text-slate-500 max-w-2xl mx-auto">Инструкции и руководства по решению типичных технических проблем.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-6 mb-12">
        <div class="flex-1 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Поиск по названию инструкции..." 
                   class="w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white">
            <span class="material-symbols-outlined absolute left-4 top-4.5 text-slate-400">search</span>
        </div>
        
        <select wire:model.live="selectedCategory" class="md:w-64 py-4 rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white">
            <option value="">Все категории</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($instructions as $instruction)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden hover:shadow-2xl transition-all duration-300 group flex flex-col">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                            {{ $instruction->category->name }}
                        </span>
                        @if($instruction->pdf_path)
                            <span class="material-symbols-outlined text-red-500" title="Доступен PDF">picture_as_pdf</span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 mb-4 group-hover:text-emerald-800 transition-colors">
                        {{ $instruction->title }}
                    </h3>

                    @if($instruction->steps)
                        <div class="space-y-3">
                            @foreach(array_slice($instruction->steps, 0, 3) as $step)
                                <div class="flex gap-3 items-start">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 text-[10px] flex items-center justify-center flex-shrink-0 font-bold mt-0.5">{{ $loop->iteration }}</span>
                                    <p class="text-sm text-slate-600 line-clamp-1">{{ $step }}</p>
                                </div>
                            @endforeach
                            @if(count($instruction->steps) > 3)
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest pl-8">И еще {{ count($instruction->steps) - 3 }} шагов...</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-2">
                    @if($instruction->pdf_path)
                        <a href="{{ asset('storage/' . $instruction->pdf_path) }}" target="_blank" 
                           class="flex-1 bg-white border border-slate-200 text-slate-700 font-bold py-3 rounded-xl text-xs text-center uppercase tracking-wider hover:bg-slate-100 transition-colors">
                            Открыть PDF
                        </a>
                    @endif
                    <button class="flex-1 bg-emerald-900 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                        Подробнее
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center text-slate-400 font-medium">
                <span class="material-symbols-outlined text-6xl mb-4 block">find_in_page</span>
                Ничего не найдено по вашему запросу.
            </div>
        @endforelse
    </div>
</div>
```

## File: resources/views/livewire/manage-instructions.blade.php
```php
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
</div>```

## File: resources/views/livewire/notification-bell.blade.php
```php
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-white hover:bg-white/10 rounded-lg transition-colors flex items-center">
        <span class="material-symbols-outlined">notifications</span>
        @if($notifications->count() > 0)
            <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-emerald-950">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" 
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-[100]"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         style="display: none;">
        
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest">Уведомления</h3>
            @if($notifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-emerald-600 font-bold hover:underline">Прочитать все</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors group">
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $notification->data['message'] }}</p>
                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-slate-300 hover:text-emerald-500 transition-colors">
                            <span class="material-symbols-outlined text-sm">done_all</span>
                        </button>
                    </div>
                    @php
                        $route = '#';
                        try {
                            if (isset($notification->data['ticket_id'])) {
                                if (auth()->user()->hasRole(['admin', 'it_support'])) {
                                    $route = route('ticket-detail', $notification->data['ticket_id']);
                                } else {
                                    // For regular users, we'd need a tracking link or similar
                                    // For now, let's just not show the link if they don't have access
                                }
                            }
                        } catch (\Exception $e) {}
                    @endphp
                    @if($route !== '#')
                        <a href="{{ $route }}" class="mt-2 inline-block text-[10px] font-bold text-emerald-600 uppercase hover:underline">Перейти к заявке</a>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs italic">
                    Нет новых уведомлений
                </div>
            @endforelse
        </div>
    </div>
</div>
```

## File: resources/views/livewire/pdf-instructions.blade.php
```php
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
</div>```

## File: resources/views/livewire/ticket-detail.blade.php
```php
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
</div>```

## File: resources/views/livewire/ticket-tracking.blade.php
```php
<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-emerald-900 uppercase tracking-tighter">Отслеживание заявки #{{ $ticket->id }}</h2>
        <p class="text-slate-500 font-medium mt-1">Создана {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-slate-800">{{ $ticket->title }}</h3>
                <p class="text-slate-500 mt-1">{{ optional($ticket->category)->name }}</p>
            </div>
            <div>
                @php
                    $statusColors = [
                        'new' => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-amber-100 text-amber-800',
                        'resolved' => 'bg-emerald-100 text-emerald-800',
                    ];
                    $statusNames = [
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'resolved' => 'Решена',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full font-bold text-sm uppercase {{ $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-800' }}">
                    {{ $statusNames[$ticket->status] ?? $ticket->status }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <h4 class="text-sm font-bold text-slate-500 uppercase mb-2">Описание проблемы</h4>
            <p class="text-slate-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
            
            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                <div>
                    <span class="block text-xs font-bold text-slate-500 uppercase">Контактное лицо</span>
                    <span class="font-medium text-slate-800">{{ $ticket->contact_name }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-500 uppercase">Ответственный специалист</span>
                    <span class="font-medium text-slate-800">{{ optional($ticket->assignee)->name ?? 'Не назначен' }}</span>
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-2xl font-bold text-slate-800 uppercase tracking-tighter mb-6">История изменений</h3>
    
    <div class="relative border-l-2 border-emerald-200 ml-3 space-y-8 pb-8">
        @forelse($ticket->activities as $activity)
            <div class="relative pl-8">
                <!-- Timeline dot -->
                <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-emerald-500 ring-4 ring-white"></div>
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                    <p class="text-sm text-slate-500 mb-1">{{ $activity->created_at->format('d.m.Y H:i') }} &mdash; <span class="font-bold">{{ optional($activity->causer)->name ?? 'Система' }}</span></p>
                    
                    <p class="text-slate-800">
                        @if($activity->event === 'created')
                            Заявка успешно зарегистрирована в системе.
                        @elseif($activity->event === 'updated')
                            @php
                                $changes = $activity->properties->toArray();
                            @endphp
                            @if(isset($changes['attributes']['status']))
                                Статус изменен на <strong>{{ $statusNames[$changes['attributes']['status']] ?? $changes['attributes']['status'] }}</strong>
                            @elseif(isset($changes['attributes']['assigned_to']))
                                Назначен специалист ИТ-отдела.
                            @else
                                Заявка обновлена.
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="pl-8 text-slate-500 italic">Событий пока нет.</div>
        @endforelse
    </div>

    {{-- CSAT (Оценка качества) --}}
    @if($ticket->status == 'resolved')
        <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-200 mt-12 shadow-sm">
            <h3 class="font-extrabold text-2xl text-emerald-900 mb-2 uppercase tracking-tighter">Оцените качество обслуживания</h3>
            <p class="text-emerald-700 mb-6">Ваша заявка решена! Пожалуйста, оцените работу нашего специалиста.</p>
            
            @if(session()->has('message'))
                <div class="bg-emerald-600 text-white p-4 rounded-lg mb-6 font-bold">
                    {{ session('message') }}
                </div>
            @endif

            @if(!$ticket->rating)
                <form wire:submit.prevent="submitFeedback">
                    <div class="flex items-center gap-6 mb-6">
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="1" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">😡</div>
                            <span class="text-xs font-bold text-emerald-800">1</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="2" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🙁</div>
                            <span class="text-xs font-bold text-emerald-800">2</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="3" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">😐</div>
                            <span class="text-xs font-bold text-emerald-800">3</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="4" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🙂</div>
                            <span class="text-xs font-bold text-emerald-800">4</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model="rating" value="5" class="hidden peer">
                            <div class="text-3xl grayscale peer-checked:grayscale-0 peer-checked:scale-125 transition-all">🤩</div>
                            <span class="text-xs font-bold text-emerald-800">5</span>
                        </label>
                    </div>
                    @error('rating') <span class="text-red-500 text-sm block mb-4">{{ $message }}</span> @enderror
                    
                    <textarea wire:model="feedback_comment" class="w-full rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 mb-4 bg-white" rows="3" placeholder="Оставьте комментарий (необязательно)"></textarea>
                    
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-wider transition-colors shadow-lg">
                        Отправить оценку
                    </button>
                </form>
            @else
                <div class="bg-white p-6 rounded-xl border border-emerald-100">
                    <div class="text-emerald-900 mb-2">
                        Ваша оценка: <strong class="text-xl">{{ $ticket->rating }} ⭐</strong>
                    </div>
                    @if($ticket->feedback_comment)
                        <div class="text-slate-600 bg-slate-50 p-4 rounded-lg italic border border-slate-100">
                            &laquo;{{ $ticket->feedback_comment }}&raquo;
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
```

## File: resources/views/login.blade.php
```php
@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto px-6 py-20">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-8">
            <h2 class="text-2xl font-bold text-emerald-900 mb-6 text-center uppercase tracking-wider">Вход в систему</h2>
            
            <form method="POST" action="/login">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase">Email</label>
                    <input type="email" name="email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-200" required>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2 uppercase">Пароль</label>
                    <input type="password" name="password" value="password" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-200" required>
                </div>
                
                <button type="submit" class="w-full bg-emerald-900 text-white font-bold py-3 rounded-lg hover:bg-emerald-800 transition-all active:scale-95 shadow-md">
                    ВОЙТИ
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100">
                <p class="text-xs text-slate-400 uppercase font-bold mb-4 text-center">Тестовые аккаунты</p>
                <div class="space-y-2">
                    <div class="p-3 bg-slate-50 rounded text-xs">
                        <span class="font-bold block">Сотрудник:</span>
                        <span class="text-slate-500">ivanov@mats.ru / password</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded text-xs">
                        <span class="font-bold block">ИТ-Специалист:</span>
                        <span class="text-slate-500">admin@mats.ru / password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

## File: resources/views/welcome.blade.php
```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */ @layer properties{@supports (((-webkit-hyphens:none)) and (not (margin-trim:inline))) or ((-moz-orient:inline) and (not (color:rgb(from red r g b)))){*,:before,:after,::backdrop{--tw-translate-x:0;--tw-translate-y:0;--tw-translate-z:0;--tw-rotate-x:initial;--tw-rotate-y:initial;--tw-rotate-z:initial;--tw-skew-x:initial;--tw-skew-y:initial;--tw-space-x-reverse:0;--tw-border-style:solid;--tw-leading:initial;--tw-font-weight:initial;--tw-tracking:initial;--tw-shadow:0 0 #0000;--tw-shadow-color:initial;--tw-shadow-alpha:100%;--tw-inset-shadow:0 0 #0000;--tw-inset-shadow-color:initial;--tw-inset-shadow-alpha:100%;--tw-ring-color:initial;--tw-ring-shadow:0 0 #0000;--tw-inset-ring-color:initial;--tw-inset-ring-shadow:0 0 #0000;--tw-ring-inset:initial;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-offset-shadow:0 0 #0000;--tw-blur:initial;--tw-brightness:initial;--tw-contrast:initial;--tw-grayscale:initial;--tw-hue-rotate:initial;--tw-invert:initial;--tw-opacity:initial;--tw-saturate:initial;--tw-sepia:initial;--tw-drop-shadow:initial;--tw-drop-shadow-color:initial;--tw-drop-shadow-alpha:100%;--tw-drop-shadow-size:initial;--tw-duration:initial;--tw-ease:initial;--tw-content:""}}}@layer theme{:root,:host{--font-sans:"Instrument Sans", ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";--font-serif:ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;--font-mono:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;--color-red-50:oklch(97.1% .013 17.38);--color-red-100:oklch(93.6% .032 17.717);--color-red-200:oklch(88.5% .062 18.334);--color-red-300:oklch(80.8% .114 19.571);--color-red-400:oklch(70.4% .191 22.216);--color-red-500:oklch(63.7% .237 25.331);--color-red-600:oklch(57.7% .245 27.325);--color-red-700:oklch(50.5% .213 27.518);--color-red-800:oklch(44.4% .177 26.899);--color-red-900:oklch(39.6% .141 25.723);--color-red-950:oklch(25.8% .092 26.042);--color-orange-50:oklch(98% .016 73.684);--color-orange-100:oklch(95.4% .038 75.164);--color-orange-200:oklch(90.1% .076 70.697);--color-orange-300:oklch(83.7% .128 66.29);--color-orange-400:oklch(75% .183 55.934);--color-orange-500:oklch(70.5% .213 47.604);--color-orange-600:oklch(64.6% .222 41.116);--color-orange-700:oklch(55.3% .195 38.402);--color-orange-800:oklch(47% .157 37.304);--color-orange-900:oklch(40.8% .123 38.172);--color-orange-950:oklch(26.6% .079 36.259);--color-amber-50:oklch(98.7% .022 95.277);--color-amber-100:oklch(96.2% .059 95.617);--color-amber-200:oklch(92.4% .12 95.746);--color-amber-300:oklch(87.9% .169 91.605);--color-amber-400:oklch(82.8% .189 84.429);--color-amber-500:oklch(76.9% .188 70.08);--color-amber-600:oklch(66.6% .179 58.318);--color-amber-700:oklch(55.5% .163 48.998);--color-amber-800:oklch(47.3% .137 46.201);--color-amber-900:oklch(41.4% .112 45.904);--color-amber-950:oklch(27.9% .077 45.635);--color-yellow-50:oklch(98.7% .026 102.212);--color-yellow-100:oklch(97.3% .071 103.193);--color-yellow-200:oklch(94.5% .129 101.54);--color-yellow-300:oklch(90.5% .182 98.111);--color-yellow-400:oklch(85.2% .199 91.936);--color-yellow-500:oklch(79.5% .184 86.047);--color-yellow-600:oklch(68.1% .162 75.834);--color-yellow-700:oklch(55.4% .135 66.442);--color-yellow-800:oklch(47.6% .114 61.907);--color-yellow-900:oklch(42.1% .095 57.708);--color-yellow-950:oklch(28.6% .066 53.813);--color-lime-50:oklch(98.6% .031 120.757);--color-lime-100:oklch(96.7% .067 122.328);--color-lime-200:oklch(93.8% .127 124.321);--color-lime-300:oklch(89.7% .196 126.665);--color-lime-400:oklch(84.1% .238 128.85);--color-lime-500:oklch(76.8% .233 130.85);--color-lime-600:oklch(64.8% .2 131.684);--color-lime-700:oklch(53.2% .157 131.589);--color-lime-800:oklch(45.3% .124 130.933);--color-lime-900:oklch(40.5% .101 131.063);--color-lime-950:oklch(27.4% .072 132.109);--color-green-50:oklch(98.2% .018 155.826);--color-green-100:oklch(96.2% .044 156.743);--color-green-200:oklch(92.5% .084 155.995);--color-green-300:oklch(87.1% .15 154.449);--color-green-400:oklch(79.2% .209 151.711);--color-green-500:oklch(72.3% .219 149.579);--color-green-600:oklch(62.7% .194 149.214);--color-green-700:oklch(52.7% .154 150.069);--color-green-800:oklch(44.8% .119 151.328);--color-green-900:oklch(39.3% .095 152.535);--color-green-950:oklch(26.6% .065 152.934);--color-emerald-50:oklch(97.9% .021 166.113);--color-emerald-100:oklch(95% .052 163.051);--color-emerald-200:oklch(90.5% .093 164.15);--color-emerald-300:oklch(84.5% .143 164.978);--color-emerald-400:oklch(76.5% .177 163.223);--color-emerald-500:oklch(69.6% .17 162.48);--color-emerald-600:oklch(59.6% .145 163.225);--color-emerald-700:oklch(50.8% .118 165.612);--color-emerald-800:oklch(43.2% .095 166.913);--color-emerald-900:oklch(37.8% .077 168.94);--color-emerald-950:oklch(26.2% .051 172.552);--color-teal-50:oklch(98.4% .014 180.72);--color-teal-100:oklch(95.3% .051 180.801);--color-teal-200:oklch(91% .096 180.426);--color-teal-300:oklch(85.5% .138 181.071);--color-teal-400:oklch(77.7% .152 181.912);--color-teal-500:oklch(70.4% .14 182.503);--color-teal-600:oklch(60% .118 184.704);--color-teal-700:oklch(51.1% .096 186.391);--color-teal-800:oklch(43.7% .078 188.216);--color-teal-900:oklch(38.6% .063 188.416);--color-teal-950:oklch(27.7% .046 192.524);--color-cyan-50:oklch(98.4% .019 200.873);--color-cyan-100:oklch(95.6% .045 203.388);--color-cyan-200:oklch(91.7% .08 205.041);--color-cyan-300:oklch(86.5% .127 207.078);--color-cyan-400:oklch(78.9% .154 211.53);--color-cyan-500:oklch(71.5% .143 215.221);--color-cyan-600:oklch(60.9% .126 221.723);--color-cyan-700:oklch(52% .105 223.128);--color-cyan-800:oklch(45% .085 224.283);--color-cyan-900:oklch(39.8% .07 227.392);--color-cyan-950:oklch(30.2% .056 229.695);--color-sky-50:oklch(97.7% .013 236.62);--color-sky-100:oklch(95.1% .026 236.824);--color-sky-200:oklch(90.1% .058 230.902);--color-sky-300:oklch(82.8% .111 230.318);--color-sky-400:oklch(74.6% .16 232.661);--color-sky-500:oklch(68.5% .169 237.323);--color-sky-600:oklch(58.8% .158 241.966);--color-sky-700:oklch(50% .134 242.749);--color-sky-800:oklch(44.3% .11 240.79);--color-sky-900:oklch(39.1% .09 240.876);--color-sky-950:oklch(29.3% .066 243.157);--color-blue-50:oklch(97% .014 254.604);--color-blue-100:oklch(93.2% .032 255.585);--color-blue-200:oklch(88.2% .059 254.128);--color-blue-300:oklch(80.9% .105 251.813);--color-blue-400:oklch(70.7% .165 254.624);--color-blue-500:oklch(62.3% .214 259.815);--color-blue-600:oklch(54.6% .245 262.881);--color-blue-700:oklch(48.8% .243 264.376);--color-blue-800:oklch(42.4% .199 265.638);--color-blue-900:oklch(37.9% .146 265.522);--color-blue-950:oklch(28.2% .091 267.935);--color-indigo-50:oklch(96.2% .018 272.314);--color-indigo-100:oklch(93% .034 272.788);--color-indigo-200:oklch(87% .065 274.039);--color-indigo-300:oklch(78.5% .115 274.713);--color-indigo-400:oklch(67.3% .182 276.935);--color-indigo-500:oklch(58.5% .233 277.117);--color-indigo-600:oklch(51.1% .262 276.966);--color-indigo-700:oklch(45.7% .24 277.023);--color-indigo-800:oklch(39.8% .195 277.366);--color-indigo-900:oklch(35.9% .144 278.697);--color-indigo-950:oklch(25.7% .09 281.288);--color-violet-50:oklch(96.9% .016 293.756);--color-violet-100:oklch(94.3% .029 294.588);--color-violet-200:oklch(89.4% .057 293.283);--color-violet-300:oklch(81.1% .111 293.571);--color-violet-400:oklch(70.2% .183 293.541);--color-violet-500:oklch(60.6% .25 292.717);--color-violet-600:oklch(54.1% .281 293.009);--color-violet-700:oklch(49.1% .27 292.581);--color-violet-800:oklch(43.2% .232 292.759);--color-violet-900:oklch(38% .189 293.745);--color-violet-950:oklch(28.3% .141 291.089);--color-purple-50:oklch(97.7% .014 308.299);--color-purple-100:oklch(94.6% .033 307.174);--color-purple-200:oklch(90.2% .063 306.703);--color-purple-300:oklch(82.7% .119 306.383);--color-purple-400:oklch(71.4% .203 305.504);--color-purple-500:oklch(62.7% .265 303.9);--color-purple-600:oklch(55.8% .288 302.321);--color-purple-700:oklch(49.6% .265 301.924);--color-purple-800:oklch(43.8% .218 303.724);--color-purple-900:oklch(38.1% .176 304.987);--color-purple-950:oklch(29.1% .149 302.717);--color-fuchsia-50:oklch(97.7% .017 320.058);--color-fuchsia-100:oklch(95.2% .037 318.852);--color-fuchsia-200:oklch(90.3% .076 319.62);--color-fuchsia-300:oklch(83.3% .145 321.434);--color-fuchsia-400:oklch(74% .238 322.16);--color-fuchsia-500:oklch(66.7% .295 322.15);--color-fuchsia-600:oklch(59.1% .293 322.896);--color-fuchsia-700:oklch(51.8% .253 323.949);--color-fuchsia-800:oklch(45.2% .211 324.591);--color-fuchsia-900:oklch(40.1% .17 325.612);--color-fuchsia-950:oklch(29.3% .136 325.661);--color-pink-50:oklch(97.1% .014 343.198);--color-pink-100:oklch(94.8% .028 342.258);--color-pink-200:oklch(89.9% .061 343.231);--color-pink-300:oklch(82.3% .12 346.018);--color-pink-400:oklch(71.8% .202 349.761);--color-pink-500:oklch(65.6% .241 354.308);--color-pink-600:oklch(59.2% .249 .584);--color-pink-700:oklch(52.5% .223 3.958);--color-pink-800:oklch(45.9% .187 3.815);--color-pink-900:oklch(40.8% .153 2.432);--color-pink-950:oklch(28.4% .109 3.907);--color-rose-50:oklch(96.9% .015 12.422);--color-rose-100:oklch(94.1% .03 12.58);--color-rose-200:oklch(89.2% .058 10.001);--color-rose-300:oklch(81% .117 11.638);--color-rose-400:oklch(71.2% .194 13.428);--color-rose-500:oklch(64.5% .246 16.439);--color-rose-600:oklch(58.6% .253 17.585);--color-rose-700:oklch(51.4% .222 16.935);--color-rose-800:oklch(45.5% .188 13.697);--color-rose-900:oklch(41% .159 10.272);--color-rose-950:oklch(27.1% .105 12.094);--color-slate-50:oklch(98.4% .003 247.858);--color-slate-100:oklch(96.8% .007 247.896);--color-slate-200:oklch(92.9% .013 255.508);--color-slate-300:oklch(86.9% .022 252.894);--color-slate-400:oklch(70.4% .04 256.788);--color-slate-500:oklch(55.4% .046 257.417);--color-slate-600:oklch(44.6% .043 257.281);--color-slate-700:oklch(37.2% .044 257.287);--color-slate-800:oklch(27.9% .041 260.031);--color-slate-900:oklch(20.8% .042 265.755);--color-slate-950:oklch(12.9% .042 264.695);--color-gray-50:oklch(98.5% .002 247.839);--color-gray-100:oklch(96.7% .003 264.542);--color-gray-200:oklch(92.8% .006 264.531);--color-gray-300:oklch(87.2% .01 258.338);--color-gray-400:oklch(70.7% .022 261.325);--color-gray-500:oklch(55.1% .027 264.364);--color-gray-600:oklch(44.6% .03 256.802);--color-gray-700:oklch(37.3% .034 259.733);--color-gray-800:oklch(27.8% .033 256.848);--color-gray-900:oklch(21% .034 264.665);--color-gray-950:oklch(13% .028 261.692);--color-zinc-50:oklch(98.5% 0 0);--color-zinc-100:oklch(96.7% .001 286.375);--color-zinc-200:oklch(92% .004 286.32);--color-zinc-300:oklch(87.1% .006 286.286);--color-zinc-400:oklch(70.5% .015 286.067);--color-zinc-500:oklch(55.2% .016 285.938);--color-zinc-600:oklch(44.2% .017 285.786);--color-zinc-700:oklch(37% .013 285.805);--color-zinc-800:oklch(27.4% .006 286.033);--color-zinc-900:oklch(21% .006 285.885);--color-zinc-950:oklch(14.1% .005 285.823);--color-neutral-50:oklch(98.5% 0 0);--color-neutral-100:oklch(97% 0 0);--color-neutral-200:oklch(92.2% 0 0);--color-neutral-300:oklch(87% 0 0);--color-neutral-400:oklch(70.8% 0 0);--color-neutral-500:oklch(55.6% 0 0);--color-neutral-600:oklch(43.9% 0 0);--color-neutral-700:oklch(37.1% 0 0);--color-neutral-800:oklch(26.9% 0 0);--color-neutral-900:oklch(20.5% 0 0);--color-neutral-950:oklch(14.5% 0 0);--color-stone-50:oklch(98.5% .001 106.423);--color-stone-100:oklch(97% .001 106.424);--color-stone-200:oklch(92.3% .003 48.717);--color-stone-300:oklch(86.9% .005 56.366);--color-stone-400:oklch(70.9% .01 56.259);--color-stone-500:oklch(55.3% .013 58.071);--color-stone-600:oklch(44.4% .011 73.639);--color-stone-700:oklch(37.4% .01 67.558);--color-stone-800:oklch(26.8% .007 34.298);--color-stone-900:oklch(21.6% .006 56.043);--color-stone-950:oklch(14.7% .004 49.25);--color-black:#000;--color-white:#fff;--spacing:.25rem;--breakpoint-sm:40rem;--breakpoint-md:48rem;--breakpoint-lg:64rem;--breakpoint-xl:80rem;--breakpoint-2xl:96rem;--container-3xs:16rem;--container-2xs:18rem;--container-xs:20rem;--container-sm:24rem;--container-md:28rem;--container-lg:32rem;--container-xl:36rem;--container-2xl:42rem;--container-3xl:48rem;--container-4xl:56rem;--container-5xl:64rem;--container-6xl:72rem;--container-7xl:80rem;--text-xs:.75rem;--text-xs--line-height:calc(1 / .75);--text-sm:.875rem;--text-sm--line-height:calc(1.25 / .875);--text-base:1rem;--text-base--line-height: 1.5 ;--text-lg:1.125rem;--text-lg--line-height:calc(1.75 / 1.125);--text-xl:1.25rem;--text-xl--line-height:calc(1.75 / 1.25);--text-2xl:1.5rem;--text-2xl--line-height:calc(2 / 1.5);--text-3xl:1.875rem;--text-3xl--line-height: 1.2 ;--text-4xl:2.25rem;--text-4xl--line-height:calc(2.5 / 2.25);--text-5xl:3rem;--text-5xl--line-height:1;--text-6xl:3.75rem;--text-6xl--line-height:1;--text-7xl:4.5rem;--text-7xl--line-height:1;--text-8xl:6rem;--text-8xl--line-height:1;--text-9xl:8rem;--text-9xl--line-height:1;--font-weight-thin:100;--font-weight-extralight:200;--font-weight-light:300;--font-weight-normal:400;--font-weight-medium:500;--font-weight-semibold:600;--font-weight-bold:700;--font-weight-extrabold:800;--font-weight-black:900;--tracking-tighter:-.05em;--tracking-tight:-.025em;--tracking-normal:0em;--tracking-wide:.025em;--tracking-wider:.05em;--tracking-widest:.1em;--leading-tight:1.25;--leading-snug:1.375;--leading-normal:1.5;--leading-relaxed:1.625;--leading-loose:2;--radius-xs:.125rem;--radius-sm:.25rem;--radius-md:.375rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--radius-3xl:1.5rem;--radius-4xl:2rem;--shadow-2xs:0 1px #0000000d;--shadow-xs:0 1px 2px 0 #0000000d;--shadow-sm:0 1px 3px 0 #0000001a, 0 1px 2px -1px #0000001a;--shadow-md:0 4px 6px -1px #0000001a, 0 2px 4px -2px #0000001a;--shadow-lg:0 10px 15px -3px #0000001a, 0 4px 6px -4px #0000001a;--shadow-xl:0 20px 25px -5px #0000001a, 0 8px 10px -6px #0000001a;--shadow-2xl:0 25px 50px -12px #00000040;--inset-shadow-2xs:inset 0 1px #0000000d;--inset-shadow-xs:inset 0 1px 1px #0000000d;--inset-shadow-sm:inset 0 2px 4px #0000000d;--drop-shadow-xs:0 1px 1px #0000000d;--drop-shadow-sm:0 1px 2px #00000026;--drop-shadow-md:0 3px 3px #0000001f;--drop-shadow-lg:0 4px 4px #00000026;--drop-shadow-xl:0 9px 7px #0000001a;--drop-shadow-2xl:0 25px 25px #00000026;--ease-in:cubic-bezier(.4, 0, 1, 1);--ease-out:cubic-bezier(0, 0, .2, 1);--ease-in-out:cubic-bezier(.4, 0, .2, 1);--animate-spin:spin 1s linear infinite;--animate-ping:ping 1s cubic-bezier(0, 0, .2, 1) infinite;--animate-pulse:pulse 2s cubic-bezier(.4, 0, .6, 1) infinite;--animate-bounce:bounce 1s infinite;--blur-xs:4px;--blur-sm:8px;--blur-md:12px;--blur-lg:16px;--blur-xl:24px;--blur-2xl:40px;--blur-3xl:64px;--perspective-dramatic:100px;--perspective-near:300px;--perspective-normal:500px;--perspective-midrange:800px;--perspective-distant:1200px;--aspect-video:16 / 9;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4, 0, .2, 1);--default-font-family:var(--font-sans);--default-mono-font-family:var(--font-mono)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}::file-selector-button{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji");font-feature-settings:var(--default-font-feature-settings,normal);font-variation-settings:var(--default-font-variation-settings,normal);-webkit-tap-highlight-color:transparent}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;-webkit-text-decoration:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:var(--default-mono-font-family,ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace);font-feature-settings:var(--default-mono-font-feature-settings,normal);font-variation-settings:var(--default-mono-font-variation-settings,normal);font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}:-moz-focusring{outline:auto}progress{vertical-align:baseline}summary{display:list-item}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{vertical-align:middle;display:block}img,video{max-width:100%;height:auto}button,input,select,optgroup,textarea{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}::file-selector-button{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}:where(select:is([multiple],[size])) optgroup{font-weight:bolder}:where(select:is([multiple],[size])) optgroup option{padding-inline-start:20px}::file-selector-button{margin-inline-end:4px}::placeholder{opacity:1}@supports (not ((-webkit-appearance:-apple-pay-button))) or (contain-intrinsic-size:1px){::placeholder{color:currentColor}@supports (color:color-mix(in lab,red,red)){::placeholder{color:color-mix(in oklab,currentcolor 50%,transparent)}}}textarea{resize:vertical}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-date-and-time-value{min-height:1lh;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-datetime-edit{padding-block:0}::-webkit-datetime-edit-year-field{padding-block:0}::-webkit-datetime-edit-month-field{padding-block:0}::-webkit-datetime-edit-day-field{padding-block:0}::-webkit-datetime-edit-hour-field{padding-block:0}::-webkit-datetime-edit-minute-field{padding-block:0}::-webkit-datetime-edit-second-field{padding-block:0}::-webkit-datetime-edit-millisecond-field{padding-block:0}::-webkit-datetime-edit-meridiem-field{padding-block:0}::-webkit-calendar-picker-indicator{line-height:1}:-moz-ui-invalid{box-shadow:none}button,input:where([type=button],[type=reset],[type=submit]){appearance:button}::file-selector-button{appearance:button}::-webkit-inner-spin-button{height:auto}::-webkit-outer-spin-button{height:auto}[hidden]:where(:not([hidden=until-found])){display:none!important}}@layer components;@layer utilities{.absolute{position:absolute}.fixed{position:fixed}.relative{position:relative}.static{position:static}.inset-0{inset:calc(var(--spacing) * 0)}.start{inset-inline-start:var(--spacing)}.top-0{top:calc(var(--spacing) * 0)}.right-0{right:calc(var(--spacing) * 0)}.container{width:100%}@media(min-width:40rem){.container{max-width:40rem}}@media(min-width:48rem){.container{max-width:48rem}}@media(min-width:64rem){.container{max-width:64rem}}@media(min-width:80rem){.container{max-width:80rem}}@media(min-width:96rem){.container{max-width:96rem}}.mx-auto{margin-inline:auto}.-mt-\[6\.6rem\]{margin-top:-6.6rem}.-mt-px{margin-top:-1px}.mt-2{margin-top:calc(var(--spacing) * 2)}.mt-4{margin-top:calc(var(--spacing) * 4)}.mt-6{margin-top:calc(var(--spacing) * 6)}.mt-8{margin-top:calc(var(--spacing) * 8)}.mr-2{margin-right:calc(var(--spacing) * 2)}.-mb-px{margin-bottom:-1px}.mb-1{margin-bottom:calc(var(--spacing) * 1)}.mb-2{margin-bottom:calc(var(--spacing) * 2)}.mb-4{margin-bottom:calc(var(--spacing) * 4)}.mb-6{margin-bottom:calc(var(--spacing) * 6)}.-ml-8{margin-left:calc(var(--spacing) * -8)}.-ml-px{margin-left:-1px}.ml-1{margin-left:calc(var(--spacing) * 1)}.ml-2{margin-left:calc(var(--spacing) * 2)}.ml-4{margin-left:calc(var(--spacing) * 4)}.ml-12{margin-left:calc(var(--spacing) * 12)}.contents{display:contents}.flex{display:flex}.grid{display:grid}.hidden{display:none}.inline-block{display:inline-block}.inline-flex{display:inline-flex}.table{display:table}.aspect-\[335\/364\]{aspect-ratio:335/364}.h-1{height:calc(var(--spacing) * 1)}.h-1\.5{height:calc(var(--spacing) * 1.5)}.h-2{height:calc(var(--spacing) * 2)}.h-2\.5{height:calc(var(--spacing) * 2.5)}.h-3{height:calc(var(--spacing) * 3)}.h-3\.5{height:calc(var(--spacing) * 3.5)}.h-5{height:calc(var(--spacing) * 5)}.h-8{height:calc(var(--spacing) * 8)}.h-14{height:calc(var(--spacing) * 14)}.h-14\.5{height:calc(var(--spacing) * 14.5)}.h-16{height:calc(var(--spacing) * 16)}.min-h-screen{min-height:100vh}.w-1{width:calc(var(--spacing) * 1)}.w-1\.5{width:calc(var(--spacing) * 1.5)}.w-2{width:calc(var(--spacing) * 2)}.w-2\.5{width:calc(var(--spacing) * 2.5)}.w-3{width:calc(var(--spacing) * 3)}.w-3\.5{width:calc(var(--spacing) * 3.5)}.w-5{width:calc(var(--spacing) * 5)}.w-8{width:calc(var(--spacing) * 8)}.w-\[438px\]{width:438px}.w-auto{width:auto}.w-full{width:100%}.max-w-6xl{max-width:var(--container-6xl)}.max-w-\[335px\]{max-width:335px}.max-w-none{max-width:none}.max-w-xl{max-width:var(--container-xl)}.flex-1{flex:1}.shrink-0{flex-shrink:0}.translate-y-0{--tw-translate-y:calc(var(--spacing) * 0);translate:var(--tw-translate-x) var(--tw-translate-y)}.transform{transform:var(--tw-rotate-x,) var(--tw-rotate-y,) var(--tw-rotate-z,) var(--tw-skew-x,) var(--tw-skew-y,)}.cursor-default{cursor:default}.cursor-not-allowed{cursor:not-allowed}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}.flex-col{flex-direction:column}.flex-col-reverse{flex-direction:column-reverse}.items-center{align-items:center}.justify-between{justify-content:space-between}.justify-center{justify-content:center}.justify-end{justify-content:flex-end}.justify-items-center{justify-items:center}.gap-2{gap:calc(var(--spacing) * 2)}.gap-3{gap:calc(var(--spacing) * 3)}.gap-4{gap:calc(var(--spacing) * 4)}:where(.space-x-1>:not(:last-child)){--tw-space-x-reverse:0;margin-inline-start:calc(calc(var(--spacing) * 1) * var(--tw-space-x-reverse));margin-inline-end:calc(calc(var(--spacing) * 1) * calc(1 - var(--tw-space-x-reverse)))}.overflow-hidden{overflow:hidden}.rounded-full{border-radius:3.40282e38px}.rounded-md{border-radius:var(--radius-md)}.rounded-sm{border-radius:var(--radius-sm)}.rounded-t-lg{border-top-left-radius:var(--radius-lg);border-top-right-radius:var(--radius-lg)}.rounded-l-md{border-top-left-radius:var(--radius-md);border-bottom-left-radius:var(--radius-md)}.rounded-r-md{border-top-right-radius:var(--radius-md);border-bottom-right-radius:var(--radius-md)}.rounded-br-lg{border-bottom-right-radius:var(--radius-lg)}.rounded-bl-lg{border-bottom-left-radius:var(--radius-lg)}.border{border-style:var(--tw-border-style);border-width:1px}.border-t{border-top-style:var(--tw-border-style);border-top-width:1px}.border-r{border-right-style:var(--tw-border-style);border-right-width:1px}.border-\[\#19140035\]{border-color:#19140035}.border-\[\#e3e3e0\]{border-color:#e3e3e0}.border-black{border-color:var(--color-black)}.border-gray-200{border-color:var(--color-gray-200)}.border-gray-300{border-color:var(--color-gray-300)}.border-gray-400{border-color:var(--color-gray-400)}.border-transparent{border-color:#0000}.bg-\[\#1b1b18\]{background-color:#1b1b18}.bg-\[\#FDFDFC\]{background-color:#fdfdfc}.bg-\[\#dbdbd7\]{background-color:#dbdbd7}.bg-\[\#fff2f2\]{background-color:#fff2f2}.bg-gray-100{background-color:var(--color-gray-100)}.bg-gray-200{background-color:var(--color-gray-200)}.bg-white{background-color:var(--color-white)}.p-6{padding:calc(var(--spacing) * 6)}.px-2{padding-inline:calc(var(--spacing) * 2)}.px-4{padding-inline:calc(var(--spacing) * 4)}.px-5{padding-inline:calc(var(--spacing) * 5)}.px-6{padding-inline:calc(var(--spacing) * 6)}.py-1{padding-block:calc(var(--spacing) * 1)}.py-1\.5{padding-block:calc(var(--spacing) * 1.5)}.py-2{padding-block:calc(var(--spacing) * 2)}.py-4{padding-block:calc(var(--spacing) * 4)}.pt-8{padding-top:calc(var(--spacing) * 8)}.pb-6{padding-bottom:calc(var(--spacing) * 6)}.pb-12{padding-bottom:calc(var(--spacing) * 12)}.text-center{text-align:center}.text-lg{font-size:var(--text-lg);line-height:var(--tw-leading,var(--text-lg--line-height))}.text-sm{font-size:var(--text-sm);line-height:var(--tw-leading,var(--text-sm--line-height))}.text-\[13px\]{font-size:13px}.leading-5{--tw-leading:calc(var(--spacing) * 5);line-height:calc(var(--spacing) * 5)}.leading-7{--tw-leading:calc(var(--spacing) * 7);line-height:calc(var(--spacing) * 7)}.leading-\[20px\]{--tw-leading:20px;line-height:20px}.leading-normal{--tw-leading:var(--leading-normal);line-height:var(--leading-normal)}.font-medium{--tw-font-weight:var(--font-weight-medium);font-weight:var(--font-weight-medium)}.font-semibold{--tw-font-weight:var(--font-weight-semibold);font-weight:var(--font-weight-semibold)}.tracking-wider{--tw-tracking:var(--tracking-wider);letter-spacing:var(--tracking-wider)}.text-\[\#1B1B18\],.text-\[\#1b1b18\]{color:#1b1b18}.text-\[\#706f6c\]{color:#706f6c}.text-\[\#F3BEC7\]{color:#f3bec7}.text-\[\#F8B803\]{color:#f8b803}.text-\[\#F53003\],.text-\[\#f53003\]{color:#f53003}.text-gray-200{color:var(--color-gray-200)}.text-gray-300{color:var(--color-gray-300)}.text-gray-400{color:var(--color-gray-400)}.text-gray-500{color:var(--color-gray-500)}.text-gray-600{color:var(--color-gray-600)}.text-gray-700{color:var(--color-gray-700)}.text-gray-800{color:var(--color-gray-800)}.text-gray-900{color:var(--color-gray-900)}.text-white{color:var(--color-white)}.uppercase{text-transform:uppercase}.underline{text-decoration-line:underline}.underline-offset-4{text-underline-offset:4px}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.opacity-100{opacity:1}.mix-blend-color{mix-blend-mode:color}.mix-blend-darken{mix-blend-mode:darken}.mix-blend-hard-light{mix-blend-mode:hard-light}.mix-blend-multiply{mix-blend-mode:multiply}.shadow{--tw-shadow:0 1px 3px 0 var(--tw-shadow-color,#0000001a), 0 1px 2px -1px var(--tw-shadow-color,#0000001a);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[0px_0px_1px_0px_rgba\(0\,0\,0\,0\.03\)\,0px_1px_2px_0px_rgba\(0\,0\,0\,0\.06\)\]{--tw-shadow:0px 0px 1px 0px var(--tw-shadow-color,#00000008), 0px 1px 2px 0px var(--tw-shadow-color,#0000000f);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[inset_0px_0px_0px_1px_rgba\(26\,26\,0\,0\.16\)\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#1a1a0029);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-sm{--tw-shadow:0 1px 3px 0 var(--tw-shadow-color,#0000001a), 0 1px 2px -1px var(--tw-shadow-color,#0000001a);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.ring-gray-300{--tw-ring-color:var(--color-gray-300)}.filter{filter:var(--tw-blur,) var(--tw-brightness,) var(--tw-contrast,) var(--tw-grayscale,) var(--tw-hue-rotate,) var(--tw-invert,) var(--tw-saturate,) var(--tw-sepia,) var(--tw-drop-shadow,)}.transition{transition-property:color,background-color,border-color,outline-color,text-decoration-color,fill,stroke,--tw-gradient-from,--tw-gradient-via,--tw-gradient-to,opacity,box-shadow,transform,translate,scale,rotate,filter,-webkit-backdrop-filter,backdrop-filter,display,content-visibility,overlay,pointer-events;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-all{transition-property:all;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-opacity{transition-property:opacity;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.delay-200{transition-delay:.2s}.delay-300{transition-delay:.3s}.delay-400{transition-delay:.4s}.duration-150{--tw-duration:.15s;transition-duration:.15s}.duration-750{--tw-duration:.75s;transition-duration:.75s}.ease-in-out{--tw-ease:var(--ease-in-out);transition-timing-function:var(--ease-in-out)}.\[--stroke-color\:\#1B1B18\]{--stroke-color:#1b1b18}.not-has-\[nav\]\:hidden:not(:has(:is(nav))){display:none}.before\:absolute:before{content:var(--tw-content);position:absolute}.before\:top-0:before{content:var(--tw-content);top:calc(var(--spacing) * 0)}.before\:top-1\/2:before{content:var(--tw-content);top:50%}.before\:bottom-0:before{content:var(--tw-content);bottom:calc(var(--spacing) * 0)}.before\:bottom-1\/2:before{content:var(--tw-content);bottom:50%}.before\:left-\[0\.4rem\]:before{content:var(--tw-content);left:.4rem}.before\:border-l:before{content:var(--tw-content);border-left-style:var(--tw-border-style);border-left-width:1px}.before\:border-\[\#e3e3e0\]:before{content:var(--tw-content);border-color:#e3e3e0}@media(hover:hover){.hover\:border-\[\#1915014a\]:hover{border-color:#1915014a}.hover\:border-\[\#19140035\]:hover{border-color:#19140035}.hover\:border-black:hover{border-color:var(--color-black)}.hover\:bg-black:hover{background-color:var(--color-black)}.hover\:bg-gray-100:hover{background-color:var(--color-gray-100)}.hover\:text-gray-400:hover{color:var(--color-gray-400)}.hover\:text-gray-700:hover{color:var(--color-gray-700)}}.focus\:border-blue-300:focus{border-color:var(--color-blue-300)}.focus\:ring:focus{--tw-ring-shadow:var(--tw-ring-inset,) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color,currentcolor);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.focus\:outline-none:focus{--tw-outline-style:none;outline-style:none}.active\:bg-gray-100:active{background-color:var(--color-gray-100)}.active\:text-gray-500:active{color:var(--color-gray-500)}.active\:text-gray-700:active{color:var(--color-gray-700)}.active\:text-gray-800:active{color:var(--color-gray-800)}@media(min-width:40rem){.sm\:flex{display:flex}.sm\:hidden{display:none}.sm\:flex-1{flex:1}.sm\:items-center{align-items:center}.sm\:justify-between{justify-content:space-between}.sm\:justify-start{justify-content:flex-start}.sm\:gap-2{gap:calc(var(--spacing) * 2)}.sm\:px-6{padding-inline:calc(var(--spacing) * 6)}.sm\:pt-0{padding-top:calc(var(--spacing) * 0)}}@media(min-width:64rem){.lg\:mt-10{margin-top:calc(var(--spacing) * 10)}.lg\:mb-0{margin-bottom:calc(var(--spacing) * 0)}.lg\:mb-6{margin-bottom:calc(var(--spacing) * 6)}.lg\:-ml-px{margin-left:-1px}.lg\:ml-0{margin-left:calc(var(--spacing) * 0)}.lg\:block{display:block}.lg\:aspect-auto{aspect-ratio:auto}.lg\:w-\[438px\]{width:438px}.lg\:max-w-4xl{max-width:var(--container-4xl)}.lg\:grow{flex-grow:1}.lg\:flex-row{flex-direction:row}.lg\:justify-center{justify-content:center}.lg\:rounded-t-none{border-top-left-radius:0;border-top-right-radius:0}.lg\:rounded-tl-lg{border-top-left-radius:var(--radius-lg)}.lg\:rounded-r-lg{border-top-right-radius:var(--radius-lg);border-bottom-right-radius:var(--radius-lg)}.lg\:rounded-br-none{border-bottom-right-radius:0}.lg\:p-8{padding:calc(var(--spacing) * 8)}.lg\:p-20{padding:calc(var(--spacing) * 20)}.lg\:px-8{padding-inline:calc(var(--spacing) * 8)}.lg\:pb-10{padding-bottom:calc(var(--spacing) * 10)}}.rtl\:flex-row-reverse:where(:dir(rtl),[dir=rtl],[dir=rtl] *){flex-direction:row-reverse}@media(prefers-color-scheme:dark){.dark\:border-\[\#3E3E3A\]{border-color:#3e3e3a}.dark\:border-\[\#eeeeec\]{border-color:#eeeeec}.dark\:border-gray-600{border-color:var(--color-gray-600)}.dark\:bg-\[\#0a0a0a\]{background-color:#0a0a0a}.dark\:bg-\[\#1D0002\]{background-color:#1d0002}.dark\:bg-\[\#3E3E3A\]{background-color:#3e3e3a}.dark\:bg-\[\#161615\]{background-color:#161615}.dark\:bg-\[\#eeeeec\]{background-color:#eeeeec}.dark\:bg-gray-700{background-color:var(--color-gray-700)}.dark\:bg-gray-800{background-color:var(--color-gray-800)}.dark\:bg-gray-900{background-color:var(--color-gray-900)}.dark\:text-\[\#1C1C1A\]{color:#1c1c1a}.dark\:text-\[\#4B0600\]{color:#4b0600}.dark\:text-\[\#391800\]{color:#391800}.dark\:text-\[\#733000\]{color:#733000}.dark\:text-\[\#A1A09A\]{color:#a1a09a}.dark\:text-\[\#EDEDEC\]{color:#ededec}.dark\:text-\[\#F61500\]{color:#f61500}.dark\:text-\[\#FF4433\]{color:#f43}.dark\:text-black{color:var(--color-black)}.dark\:text-gray-200{color:var(--color-gray-200)}.dark\:text-gray-300{color:var(--color-gray-300)}.dark\:text-gray-400{color:var(--color-gray-400)}.dark\:text-gray-600{color:var(--color-gray-600)}.dark\:mix-blend-hard-light{mix-blend-mode:hard-light}.dark\:mix-blend-normal{mix-blend-mode:normal}.dark\:shadow-\[inset_0px_0px_0px_1px_\#fffaed2d\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#fffaed2d);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.dark\:\[--stroke-color\:\#FF750F\]{--stroke-color:#ff750f}.dark\:before\:border-\[\#3E3E3A\]:before{content:var(--tw-content);border-color:#3e3e3a}@media(hover:hover){.dark\:hover\:border-\[\#3E3E3A\]:hover{border-color:#3e3e3a}.dark\:hover\:border-\[\#62605b\]:hover{border-color:#62605b}.dark\:hover\:border-white:hover{border-color:var(--color-white)}.dark\:hover\:bg-gray-900:hover{background-color:var(--color-gray-900)}.dark\:hover\:bg-white:hover{background-color:var(--color-white)}.dark\:hover\:text-gray-200:hover{color:var(--color-gray-200)}.dark\:hover\:text-gray-300:hover{color:var(--color-gray-300)}}.dark\:focus\:border-blue-700:focus{border-color:var(--color-blue-700)}.dark\:focus\:border-blue-800:focus{border-color:var(--color-blue-800)}.dark\:active\:bg-gray-700:active{background-color:var(--color-gray-700)}.dark\:active\:text-gray-300:active{color:var(--color-gray-300)}}@starting-style{.starting\:opacity-0{opacity:0}}@media(prefers-reduced-motion:no-preference){@starting-style{.motion-safe\:starting\:-translate-x-\[26px\]{--tw-translate-x: -26px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[51px\]{--tw-translate-x: -51px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[78px\]{--tw-translate-x: -78px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[102px\]{--tw-translate-x: -102px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:translate-y-6{--tw-translate-y:calc(var(--spacing) * 6);translate:var(--tw-translate-x) var(--tw-translate-y)}}}}@property --tw-translate-x{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-y{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-z{syntax:"*";inherits:false;initial-value:0}@property --tw-rotate-x{syntax:"*";inherits:false}@property --tw-rotate-y{syntax:"*";inherits:false}@property --tw-rotate-z{syntax:"*";inherits:false}@property --tw-skew-x{syntax:"*";inherits:false}@property --tw-skew-y{syntax:"*";inherits:false}@property --tw-space-x-reverse{syntax:"*";inherits:false;initial-value:0}@property --tw-border-style{syntax:"*";inherits:false;initial-value:solid}@property --tw-leading{syntax:"*";inherits:false}@property --tw-font-weight{syntax:"*";inherits:false}@property --tw-tracking{syntax:"*";inherits:false}@property --tw-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-shadow-color{syntax:"*";inherits:false}@property --tw-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-inset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-shadow-color{syntax:"*";inherits:false}@property --tw-inset-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-ring-color{syntax:"*";inherits:false}@property --tw-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-ring-color{syntax:"*";inherits:false}@property --tw-inset-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-ring-inset{syntax:"*";inherits:false}@property --tw-ring-offset-width{syntax:"<length>";inherits:false;initial-value:0}@property --tw-ring-offset-color{syntax:"*";inherits:false;initial-value:#fff}@property --tw-ring-offset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-blur{syntax:"*";inherits:false}@property --tw-brightness{syntax:"*";inherits:false}@property --tw-contrast{syntax:"*";inherits:false}@property --tw-grayscale{syntax:"*";inherits:false}@property --tw-hue-rotate{syntax:"*";inherits:false}@property --tw-invert{syntax:"*";inherits:false}@property --tw-opacity{syntax:"*";inherits:false}@property --tw-saturate{syntax:"*";inherits:false}@property --tw-sepia{syntax:"*";inherits:false}@property --tw-drop-shadow{syntax:"*";inherits:false}@property --tw-drop-shadow-color{syntax:"*";inherits:false}@property --tw-drop-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-drop-shadow-size{syntax:"*";inherits:false}@property --tw-duration{syntax:"*";inherits:false}@property --tw-ease{syntax:"*";inherits:false}@property --tw-content{syntax:"*";inherits:false;initial-value:""}@keyframes spin{to{transform:rotate(360deg)}}@keyframes ping{75%,to{opacity:0;transform:scale(2)}}@keyframes pulse{50%{opacity:.5}}@keyframes bounce{0%,to{animation-timing-function:cubic-bezier(.8,0,1,1);transform:translateY(-25%)}50%{animation-timing-function:cubic-bezier(0,0,.2,1);transform:none}}
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-6 lg:p-20 lg:pb-10 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="mb-1 font-medium">Let's get started</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">With so many options available to you,<br /> we suggest you start with the following:</p>
                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Documentation</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Laracasts</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a href="https://cloud.laravel.com" target="_blank" class="inline-block dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black px-5 py-1.5 bg-[#1b1b18] rounded-sm border border-black text-white text-sm leading-normal">
                                Deploy now
                            </a>
                        </li>
                    </ul>

                    <p class="mt-6 lg:mt-10 text-[#706f6c] dark:text-[#A1A09A]">
                        v{{ app()->version() }}
                        <a href="https://github.com/laravel/framework/blob/13.x/CHANGELOG.md" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                            <span>View changelog</span>
                            <svg
                                width="10"
                                height="11"
                                viewBox="0 0 10 11"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-2.5 h-2.5"
                            >
                                <path
                                    d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                    stroke="currentColor"
                                    stroke-linecap="square"
                                />
                            </svg>
                        </a>
                    </p>
                </div>
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/364] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                    {{-- Laravel Logo --}}
                    <svg class="w-full text-[#F53003] dark:text-[#F61500] transition-all translate-y-0 opacity-100 max-w-none duration-750 starting:opacity-0 motion-safe:starting:translate-y-6" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                        <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                        <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                        <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                        <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
                    </svg>

                    {{-- 13 --}}
                    <svg class="w-[438px] max-w-none relative -mt-[6.6rem] -ml-8 lg:ml-0 [--stroke-color:#1B1B18] dark:[--stroke-color:#FF750F]" viewBox="0 0 440 392" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g class="mix-blend-darken dark:mix-blend-normal transition-all delay-300 opacity-100 duration-750 starting:opacity-0 text-[#1B1B18] dark:text-black">
                            <mask id="path-1-mask" maskUnits="userSpaceOnUse" x="-0.328613" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="-0.328613" y="103" width="338" height="299"/>
                                <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z"/>
                                <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z"/>
                            </mask>
                            <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z" fill="currentColor"/>
                            <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z" fill="currentColor"/>
                            <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-1-mask)"/>
                            <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-1-mask)"/>
                        </g>

                        <g class="transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[26px] text-[#F3BEC7] dark:text-[#4B0600]">
                            <mask id="path-2-mask" maskUnits="userSpaceOnUse" x="25.3357" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="25.3357" y="103" width="338" height="299"/>
                                <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z"/>
                                <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z"/>
                            </mask>
                            <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z" fill="currentColor"/>
                            <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z" fill="currentColor"/>
                            <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-2-mask)"/>
                            <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-2-mask)"/>
                        </g>
                        
                        <g class="mix-blend-color dark:mix-blend-hard-light transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[51px] text-[#F8B803] dark:text-[#391800]">
                            <mask id="path-3-mask" maskUnits="userSpaceOnUse" x="51" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="51" y="103" width="338" height="299"/>
                                <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z"/>
                                <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z"/>
                            </mask>
                            <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z" fill="currentColor"/>
                            <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z" fill="currentColor"/>
                            <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-3-mask)"/>
                            <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-3-mask)"/>
                        </g>
                        
                        <g class="mix-blend-multiply dark:mix-blend-normal transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[78px] text-[#F3BEC7] dark:text-[#733000]">
                            <mask id="path-4-mask" maskUnits="userSpaceOnUse" x="76.6643" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="76.6643" y="103" width="338" height="299"/>
                                <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z"/>
                                <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z"/>
                            </mask>
                            <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z" fill="currentColor"/>
                            <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z" fill="currentColor"/>
                            <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-4-mask)"/>
                            <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-4-mask)"/>
                        </g>
                        
                        <g class="mix-blend-hard-light transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[102px] text-[#F3BEC7] dark:text-[#4B0600]">
                            <mask id="path-5-mask" maskUnits="userSpaceOnUse" x="102.329" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="102.329" y="103" width="338" height="299"/>
                                <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z"/>
                                <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z"/>
                            </mask>
                            <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z" fill="currentColor"/>
                            <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z" fill="currentColor"/>
                            <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-5-mask)"/>
                            <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-5-mask)"/>
                        </g>
                    </svg>
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
```

## File: routes/web.php
```php
<?php

use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\AssetManagement;
use App\Livewire\Admin\BrandManagement;
use App\Livewire\Admin\CategoryManagement;
use App\Livewire\Admin\DirectoryManagement;
use App\Livewire\Admin\EmployeeManagement;
use App\Livewire\Admin\PartManagement;
use App\Livewire\Admin\ResourceManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\CreateTicket;
use App\Livewire\ItDashboard;
use App\Livewire\KnowledgeBase;
use App\Livewire\ManageInstructions;
use App\Livewire\PdfInstructions;
use App\Livewire\TicketDetail;
use App\Livewire\TicketTracking;
use App\Models\GovernmentResource;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'resources' => GovernmentResource::all(),
    ]);
})->name('home');

Route::get('/directory', function () {
    return view('directory');
})->name('directory');

Route::get('/support', CreateTicket::class)->name('support');
Route::get('/knowledge-base', KnowledgeBase::class)->name('knowledge-base');
Route::get('/track/{uuid}', TicketTracking::class)->name('track');

Route::middleware(['auth'])->group(function () {
    // Shared for Technicians and Admins
    Route::middleware(['role:it_support|admin'])->group(function () {
        Route::get('/it-dashboard', ItDashboard::class)->name('it-dashboard');
        Route::get('/tickets/{ticket}', TicketDetail::class)->name('ticket-detail');
        Route::get('/manage-instructions', ManageInstructions::class)->name('manage-instructions');
        Route::get('/admin/assets', AssetManagement::class)->name('admin.assets');
        Route::get('/admin/analytics', AnalyticsDashboard::class)->name('admin.analytics');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/categories', CategoryManagement::class)->name('admin.categories');
        Route::get('/admin/parts', PartManagement::class)->name('admin.parts');
        Route::get('/admin/brands', BrandManagement::class)->name('admin.brands');
        Route::get('/admin/pdf-instructions', PdfInstructions::class)->name('admin.pdf-instructions');
    });

    // Manager and Admin
    Route::middleware(['role:manager|admin'])->group(function () {
        Route::get('/admin/users', UserManagement::class)->name('admin.users');
        Route::get('/admin/employees', EmployeeManagement::class)->name('admin.employees');
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
```

## File: resources/css/app.css
```css
@import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap");

@import "tailwindcss";
@plugin "@tailwindcss/forms";

@source "../../vendor/power-components/livewire-powergrid/resources/views/**/*.php";
@source "../../vendor/power-components/livewire-powergrid/src/**/*.php";

@theme {
    --color-surface-container: #f0eded;
    --color-on-secondary: #ffffff;
    --color-surface-variant: #e4e2e1;
    --color-secondary-container: #fed488;
    --color-surface: #fcf9f8;
    --color-primary-container: #1b4332;
    --color-surface-container-low: #f6f3f2;
    --color-surface-container-high: #eae7e7;
    --color-on-surface: #1b1c1c;
    --color-on-primary: #ffffff;
    --color-primary: #012d1d;
    --color-background: #fcf9f8;
    --color-secondary: #775a19;
    --font-body-lg: "Public Sans", sans-serif;
    
    --color-primary-fixed-variant: #274e3d;
    --color-outline: #717973;
    --color-outline-variant: #c1c8c2;

    /* PowerGrid Fixes - Light Theme */
    --color-pg-primary-50: #f8fafc;
    --color-pg-primary-100: #f1f5f9;
    --color-pg-primary-200: #e2e8f0;
    --color-pg-primary-500: #0f172a;
    --color-pg-primary-600: #1e293b;
}

body {
    font-family: "Public Sans", sans-serif;
    background-color: var(--color-background);
    color: var(--color-on-surface);
}

/* PowerGrid Structural Fixes - Strict White Design */
.pg-table-container { @apply bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm; }
table { @apply min-w-full bg-white divide-y divide-slate-200; }
thead { @apply bg-slate-50; }
th { @apply px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200; }
tbody { @apply bg-white divide-y divide-slate-100; }
td { @apply px-6 py-4 text-sm text-slate-900 font-medium; }
tr:hover { @apply bg-slate-50; }

/* Navigation & Elements visibility */
.nav-link { @apply text-emerald-100 hover:text-white transition-colors px-3 py-2 rounded-lg; }
.nav-link.active { @apply bg-white/10 text-white font-bold; }

/* Instruction List */
.instruction-list { @apply space-y-3; }
.instruction-list li { @apply flex items-start gap-3 p-3 bg-slate-50 rounded-lg text-slate-700 text-sm border border-slate-100; }
.instruction-list li::before { content: "✓"; @apply text-emerald-600 font-bold; }
```

