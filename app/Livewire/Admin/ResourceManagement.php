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
