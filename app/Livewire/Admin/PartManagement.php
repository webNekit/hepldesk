<?php

namespace App\Livewire\Admin;

use App\Models\Part;
use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;
use WithPagination;

class PartManagement extends Component
{
    use WithPagination;

    public $parts;
    public $name;
    public $sku;
    public $category_id;
    public $brand_id;
    public $quantity;
    public $description;
    public $editingPartId;

    public $categories;
    public $brands;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'nullable|string|max:100|unique:parts,sku',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'required|exists:brands,id',
        'quantity' => 'required|integer|min:0',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->categories = Category::all();
        $this->brands = Brand::all();
        $this->resetPartForm();
    }

    public function resetPartForm()
    {
        $this->name = '';
        $this->sku = '';
        $this->category_id = '';
        $this->brand_id = '';
        $this->quantity = 0;
        $this->description = '';
        $this->editingPartId = null;
    }

    public function render()
    {
        return view('livewire.admin.part-management', [
            'parts' => Part::with(['category', 'brand'])->paginate(10),
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

        session()->flash('message', 'Запчасть создана!');
        $this->resetPartForm();
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
    }

    public function updatePart()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:parts,sku,' . $this->editingPartId,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Part::find($this->editingPartId)->update([
            'name' => $this->name,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'quantity' => $this->quantity,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Запчасть обновлена!');
        $this->resetPartForm();
    }

    public function deletePart($id)
    {
        Part::find($id)->delete();
        session()->flash('message', 'Запчасть удалена!');
    }

    public function usePart($id, $quantity = 1)
    {
        $part = Part::findOrFail($id);
        
        if ($part->quantity < $quantity) {
            session()->flash('error', 'Недостаточно запчастей на складе! Доступно: ' . $part->quantity);
            return;
        }

        $part->decrement('quantity', $quantity);
        session()->flash('message', 'Запчасть использована! Осталось на складе: ' . ($part->quantity - $quantity));
    }
}