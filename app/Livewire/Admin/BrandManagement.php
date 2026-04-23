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
}