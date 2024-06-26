<?php

namespace App\Models;

use App\Filament\Resources\RoomResource\Pages\DeskBookings;
use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array'
    ];

    protected $guarded = [];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
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

    public function desks()
    {
        return $this->hasMany(Desk::class);
    }

    public function deskBookings()
    {
        return $this->hasManyThrough(DeskBooking::class, Desk::class);
    }
}
