<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array',
        'active' => 'boolean'
    ];

    protected $guarded = [];

    protected $appends = ['latlng'];

    public function getLatlngAttribute()
    {
        return data_get($this, 'attributes.latlng');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
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

    public function bookings()
    {
        return $this->hasMany(DeskBooking::class);
    }
}
