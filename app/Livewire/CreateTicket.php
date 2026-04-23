<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Instruction;
use App\Models\Ticket;
use Livewire\Component;

class CreateTicket extends Component
{
    public $categories;
    public $category_id;
    public $instruction;
    public $title;
    public $description;
    public $priority = 'normal';

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatedCategoryId($value)
    {
        if ($value) {
            $this->instruction = Instruction::where('category_id', $value)->first();
        } else {
            $this->instruction = null;
        }
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:normal,high',
        ]);

        $category = Category::find($this->category_id);

        $ticket = Ticket::create([
            'user_id' => auth()->id() ?? 2,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assigned_to' => $category->default_assignee_id,
            'status' => 'new',
        ]);

        session()->flash('message', 'Заявка успешно создана! Номер заявки: #' . $ticket->id);

        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.create-ticket')->layout('layouts.app');
    }
}
