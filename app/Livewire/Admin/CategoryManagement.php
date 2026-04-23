<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;

class CategoryManagement extends Component
{
    public $categories;
    public $name;
    public $editingCategoryId;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function render()
    {
        return view('livewire.admin.category-management', [
            'categories' => Category::withCount(['parts', 'instructions', 'tickets'])->get(),
        ])->layout('layouts.admin');
    }

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
        ]);

        session()->flash('message', 'Категория создана!');
        $this->reset(['name']);
        $this->categories = Category::all();
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->name = $category->name;
    }

    public function updateCategory()
    {
        $this->validate();

        Category::find($this->editingCategoryId)->update([
            'name' => $this->name,
        ]);

        session()->flash('message', 'Категория обновлена!');
        $this->reset(['name', 'editingCategoryId']);
        $this->categories = Category::all();
    }

    public function deleteCategory($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Категория удалена!');
        $this->categories = Category::all();
    }
}