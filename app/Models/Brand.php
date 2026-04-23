<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }
}
