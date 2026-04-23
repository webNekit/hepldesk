<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;

class ManageInstructions extends Component
{
    public $categories;
    public $category_id;
    public $title;
    public $steps = []; // Array for repeater
    public $editingInstructionId;

    protected $rules = [
        'category_id' => 'required|exists:categories,id',
        'title' => 'required|string|max:255',
        'steps' => 'required|array|min:1',
        'steps.*' => 'required|string|min:3',
    ];

    public function mount()
    {
        $this->categories = Category::all();
        $this->addStep(); // Add first step by default
    }

    public function addStep()
    {
        $this->steps[] = '';
    }

    public function removeStep($index)
    {
        unset($this->steps[$index]);
        $this->steps = array_values($this->steps);
    }

    public function edit($id)
    {
        $instruction = Instruction::findOrFail($id);
        $this->editingInstructionId = $id;
        $this->category_id = $instruction->category_id;
        $this->title = $instruction->title;
        $this->steps = $instruction->steps ?? [''];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'title' => $this->title,
            'steps' => $this->steps,
        ];

        if ($this->editingInstructionId) {
            Instruction::find($this->editingInstructionId)->update($data);
            session()->flash('message', 'Инструкция обновлена!');
        } else {
            Instruction::create($data);
            session()->flash('message', 'Инструкция создана!');
        }

        $this->reset(['category_id', 'title', 'editingInstructionId']);
        $this->steps = [''];
    }

    public function delete($id)
    {
        Instruction::find($id)->delete();
        session()->flash('message', 'Инструкция удалена!');
    }

    public function render()
    {
        return view('livewire.manage-instructions', [
            'instructions' => Instruction::with('category')->get(),
        ])->layout('layouts.admin');
    }
}
