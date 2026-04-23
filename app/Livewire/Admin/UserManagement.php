<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    public $name, $email, $password, $department_id, $position, $phone, $cabinet, $selected_roles = [];
    public $editingUserId;

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department_id = $user->department_id;
        $this->position = $user->position;
        $this->phone = $user->phone;
        $this->cabinet = $user->cabinet;
        $this->selected_roles = $user->roles->pluck('name')->toArray();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingUserId,
            'department_id' => 'nullable|exists:departments,id',
            'selected_roles' => 'required|array|min:1',
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'position' => $this->position,
            'phone' => $this->phone,
            'cabinet' => $this->cabinet,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editingUserId) {
            $user = User::find($this->editingUserId);
            $user->update($data);
            $user->syncRoles($this->selected_roles);
            session()->flash('message', 'Пользователь обновлен!');
        } else {
            $user = User::create($data);
            $user->assignRole($this->selected_roles);
            session()->flash('message', 'Пользователь создан!');
        }

        $this->reset(['name', 'email', 'password', 'department_id', 'position', 'phone', 'cabinet', 'selected_roles', 'editingUserId']);
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => User::with('roles', 'department')->get(),
            'departments' => Department::all(),
            'roles' => Role::all(),
        ])->layout('layouts.admin');
    }
}
