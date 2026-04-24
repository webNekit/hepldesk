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
