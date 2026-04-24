<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Instruction;
use App\Models\Part;
use App\Models\Ticket;
use App\Notifications\NewCommentNotification;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Support\Str;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;

    public $body;

    public $instruction; // текстовая (если есть)

    public $pdfInstruction; // 🔥 PDF

    public $showInstruction = false;

    public $part_id;

    public $asset_id;

    public $rating;

    public $feedback_comment;

    public function submitFeedback()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string',
        ]);

        $this->ticket->update([
            'rating' => $this->rating,
            'feedback_comment' => $this->feedback_comment,
        ]);

        session()->flash('message', 'Спасибо за вашу оценку!');
    }

    public function generateUuid()
    {
        if (! $this->ticket->uuid) {
            $this->ticket->update(['uuid' => (string) Str::uuid()]);
            session()->flash('message', 'Публичный ключ успешно сгенерирован!');
        }
    }

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->asset_id = $ticket->asset_id;

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
        $this->showInstruction = ! $this->showInstruction;
    }

    public function getAvailablePartsProperty()
    {
        return Part::where('category_id', $this->ticket->category_id)->get();
    }

    public function attachPart()
    {
        if (! $this->part_id) {
            return;
        }

        $part = Part::findOrFail($this->part_id);

        if ($part->quantity > 0) {
            $part->decrement('quantity');

            $this->ticket->comments()->create([
                'user_id' => auth()->id(),
                'body' => 'Использована запчасть: '.$part->name,
            ]);

            session()->flash('message', 'Запчасть успешно списана!');
        } else {
            session()->flash('error', 'Запчасть закончилась!');
        }
    }

    public function updateStatus($status)
    {
        $this->ticket->update(['status' => $status]);

        // Уведомляем автора заявки
        $this->ticket->user->notify(new TicketStatusUpdated($this->ticket, $status));

        session()->flash('message', 'Статус обновлен!');
    }

    public function addComment()
    {
        if (! $this->body) {
            return;
        }

        $comment = $this->ticket->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->body,
        ]);

        // Уведомляем техзадание/автора (кто не является автором комментария)
        $recipients = collect([$this->ticket->user, $this->ticket->assignee])
            ->filter()
            ->unique('id')
            ->reject(fn ($u) => $u->id === auth()->id());

        foreach ($recipients as $recipient) {
            $recipient->notify(new NewCommentNotification($comment));
        }

        $this->reset('body');
    }

    public function updateAsset()
    {
        $this->ticket->update(['asset_id' => $this->asset_id]);
        session()->flash('message', 'Оборудование привязано к заявке!');
    }

    public function render()
    {
        return view('livewire.ticket-detail', [
            'parts' => $this->availableParts,
            'assets' => Asset::all(),
        ])->layout('layouts.admin');
    }
}
