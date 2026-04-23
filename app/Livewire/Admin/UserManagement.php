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