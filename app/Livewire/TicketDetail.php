<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Instruction;
use App\Models\Ticket;
use App\Models\Part;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;

    public $body;
    public $instruction; // текстовая (если есть)
    public $pdfInstruction; // 🔥 PDF
    public $showInstruction = false;
    public $part_id;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;

        // ТЕКСТОВАЯ инструкция (если нужна)
        $this->instruction = Instruction::where('category_id', $ticket->category_id)
            ->whereNull('pdf_path')
            ->first();

        // 🔥 PDF инструкция
        $this->pdfInstruction = Instruction::where('category_id', $ticket->category_id)
            ->whereNotNull('pdf_path')
            ->first();
    }

    public function toggleInstruction()
    {
        $this->showInstruction = !$this->showInstruction;
    }

    public function getAvailablePartsProperty()
    {
        return Part::where('category_id', $this->ticket->category_id)->get();
    }

    public function attachPart()
    {
        if (!$this->part_id)
            return;

        $part = Part::findOrFail($this->part_id);

        if ($part->quantity > 0) {
            $part->decrement('quantity');

            $this->ticket->comments()->create([
                'user_id' => auth()->id(),
                'body' => "Использована запчасть: " . $part->name
            ]);

            session()->flash('message', 'Запчасть успешно списана!');
        } else {
            session()->flash('error', 'Запчасть закончилась!');
        }
    }

    public function updateStatus($status)
    {
        $this->ticket->update(['status' => $status]);
        session()->flash('message', 'Статус обновлен!');
    }

    public function addComment()
    {
        if (!$this->body)
            return;

        $this->ticket->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->body
        ]);

        $this->reset('body');
    }

    public function render()
    {
        return view('livewire.ticket-detail', [
            'parts' => $this->availableParts
        ])->layout('layouts.admin');
    }
}