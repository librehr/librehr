<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array'
    ];

    protected $guarded = [];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
