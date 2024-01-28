<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskBooking extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array',
        'start' => 'DateTime',
        'end' => 'DateTime',
    ];

    protected $guarded = [];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
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

    public function desk()
    {
        return $this->belongsTo(Desk::class);
    }
}
