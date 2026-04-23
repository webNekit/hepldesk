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
