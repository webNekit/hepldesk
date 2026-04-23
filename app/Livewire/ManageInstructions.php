<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class ManageInstructions extends Component
{
    public $title, $category_id, $steps = [''], $editingId;
    public $showModal = false;

    public function addStep() { $this->steps[] = ''; }
    public function removeStep($index) { unset($this->steps[$index]); $this->steps = array_values($this->steps); }

    public function openModal() { $this->showModal = true; }
    public function closeModal() { $this->reset(['title', 'category_id', 'steps', 'editingId']); $this->showModal = false; }

    public function save() {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'steps' => 'required|array']);
        Instruction::updateOrCreate(['id' => $this->editingId], [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'steps' => $this->steps
        ]);
        $this->closeModal();
    }

    public function render() {
        return view('livewire.manage-instructions', [
            'instructions' => Instruction::whereNull('pdf_path')->with('category')->get(),
            'categories' => Category::all()
        ])->layout('layouts.admin');
    }
}