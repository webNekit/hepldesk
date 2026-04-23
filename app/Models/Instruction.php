<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_id', 'title', 'steps'])]
class Instruction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'steps' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
