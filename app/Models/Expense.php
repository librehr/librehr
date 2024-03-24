<?php

namespace App\Models;

use App\Enums\ExpenseStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => ExpenseStatusEnum::class,
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

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
