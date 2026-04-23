<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'default_assignee_id'])]
class Category extends Model
{
    use HasFactory;

    public function defaultAssignee()
    {
        return $this->belongsTo(User::class, 'default_assignee_id');
    }

    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
