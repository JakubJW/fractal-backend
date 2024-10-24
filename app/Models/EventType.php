<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    protected $fillable = [
        'id',
        'title',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
