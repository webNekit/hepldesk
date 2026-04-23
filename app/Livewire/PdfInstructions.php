<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Instruction;
use App\Models\Category;

class PdfInstructions extends Component
{
    use WithFileUploads;

    public $title;
    public $category_id;
    public $pdf;

    public $showModal = false;

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
        $this->reset(['title', 'category_id', 'pdf']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        // 🔥 Сохраняем файл
        $path = $this->pdf->store('instructions', 'public');

        Instruction::create([
            'title' => $this->title,
            'category_id' => $this->category_id,
            'pdf_path' => $path,
            'steps' => null, // важно чтобы не ломалось
        ]);

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