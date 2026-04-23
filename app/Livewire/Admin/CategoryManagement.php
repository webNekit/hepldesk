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
}