<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class ManageInstructions extends Component
{
    public $title;

    public $category_id;

    public $steps = [''];

    public $editingId;

    public $showModal = false;

    public function addStep()
    {
        $this->steps[] = '';
    }

    public function removeStep($index)
    {
        unset($this->steps[$index]);
        $this->steps = array_values($this->steps);
    }

    public function openModal()
    {
        $this->reset(['title', 'category_id', 'steps', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['title', 'category_id', 'steps', 'editingId']);
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'steps' => 'required|array']);
        Instruction::updateOrCreate(['id' => $this->editingId], [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'steps' => $this->steps,
        ]);
        $this->closeModal();
    }

    public function edit($id)
    {
        $instruction = Instruction::findOrFail($id);
        $this->editingId = $instruction->id;
        $this->title = $instruction->title;
        $this->category_id = $instruction->category_id;
        $this->steps = $instruction->steps ?? [''];
        $this->showModal = true;
    }

    public function delete($id)
    {
        Instruction::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.manage-instructions', [
            'instructions' => Instruction::whereNull('pdf_path')->with('category')->get(),
            'categories' => Category::all(),
        ])->layout('layouts.admin');
    }
}
