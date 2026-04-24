<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PdfInstructions extends Component
{
    use WithFileUploads;

    public $title;

    public $category_id;

    public $pdf;

    public $showModal = false;

    public $editingId;

    public $categories = [];

    public $instructions = [];

    public function mount()
    {
        $this->categories = Category::all();
        $this->loadInstructions();
    }

    public function loadInstructions()
    {
        $this->instructions = Instruction::whereNotNull('pdf_path')->get();
    }

    public function openModal()
    {
        $this->reset(['title', 'category_id', 'pdf', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['title', 'category_id', 'pdf', 'editingId']);
        $this->showModal = false;
    }

    public function edit($id)
    {
        $instruction = Instruction::findOrFail($id);
        $this->editingId = $instruction->id;
        $this->title = $instruction->title;
        $this->category_id = $instruction->category_id;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $instruction = Instruction::findOrFail($id);
        if ($instruction->pdf_path) {
            Storage::disk('public')->delete($instruction->pdf_path);
        }
        $instruction->delete();
        $this->loadInstructions();
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ];

        if (! $this->editingId || $this->pdf) {
            $rules['pdf'] = 'required|file|mimes:pdf|max:10240';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'category_id' => $this->category_id,
        ];

        if ($this->pdf) {
            // Удаляем старый файл при замене
            if ($this->editingId) {
                $oldInst = Instruction::find($this->editingId);
                if ($oldInst && $oldInst->pdf_path) {
                    Storage::disk('public')->delete($oldInst->pdf_path);
                }
            }
            $data['pdf_path'] = $this->pdf->store('instructions', 'public');
            $data['steps'] = null;
        }

        Instruction::updateOrCreate(['id' => $this->editingId], $data);

        $this->closeModal();
        $this->loadInstructions();

        session()->flash('message', 'PDF инструкция добавлена!');
    }

    public function render()
    {
        return view('livewire.pdf-instructions')
            ->layout('layouts.admin');
    }
}
