<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array',
        'date' => 'date',
        'paid_at' => 'datetime',
        'paid' => 'bool',
    ];

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    protected function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
