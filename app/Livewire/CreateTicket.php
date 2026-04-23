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
    public $instructions = [];

    public $title, $description, $priority = 'normal';
    public $contact_name, $contact_phone, $contact_email;

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatedCategoryId($value)
    {
        if (!$value) {
            $this->instructions = [];
            return;
        }

        // Берём ВСЕ клиентские инструкции (без PDF)
        $this->instructions = Instruction::where('category_id', $value)
            ->whereNull('pdf_path')
            ->get();
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'contact_email' => 'required|email',
        ]);

        $category = Category::with('technicians')->find($this->category_id);

        $assignedTo = $category->technicians()
            ->withCount(['assignedTickets' => fn($q) => $q->whereIn('status', ['new', 'in_progress'])])
            ->orderBy('assigned_tickets_count', 'asc')
            ->first()?->id;

        Ticket::create([
            'user_id' => auth()->id() ?? 1,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assigned_to' => $assignedTo,
            'status' => 'new',
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
        ]);

        session()->flash('message', 'Заявка создана!');
        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.create-ticket')
            ->layout('layouts.app');
    }
}