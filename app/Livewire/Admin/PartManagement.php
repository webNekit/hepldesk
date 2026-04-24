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
