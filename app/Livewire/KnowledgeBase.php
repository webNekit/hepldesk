<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class KnowledgeBase extends Component
{
    public $search = '';

    public $selectedCategory = '';

    public function render()
    {
        $instructions = Instruction::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->with('category')
            ->get();

        return view('livewire.knowledge-base', [
            'instructions' => $instructions,
            'categories' => Category::all(),
        ])->layout('layouts.app');
    }
}
