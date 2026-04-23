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