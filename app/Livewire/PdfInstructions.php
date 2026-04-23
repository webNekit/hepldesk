<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use Livewire\Component;
use Livewire\WithFileUploads;

class PdfInstructions extends Component
{
    use WithFileUploads;

    public $title, $category_id, $pdf, $editingId, $showModal = false;

    public function openModal() { $this->showModal = true; }
    public function closeModal() { $this->reset(); $this->showModal = false; }

    public function save() {
        $this->validate(['title' => 'required', 'category_id' => 'required', 'pdf' => 'required|file|mimes:pdf']);
        $path = $this->pdf->store('instructions', 'public');
        Instruction::create(['title' => $this->title, 'category_id' => $this->category_id, 'pdf_path' => $path]);
        $this->closeModal();
    }

    public function render() {
        return view('livewire.pdf-instructions', [
            'instructions' => Instruction::whereNotNull('pdf_path')->get(),
            'categories' => Category::all()
        ])->layout('layouts.admin');
    }
}