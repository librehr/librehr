<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceValidation extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
        'attributes' => 'array',
        'validated' => 'boolean'
    ];

    protected $guarded = [];

    protected $appends = [
        'YearMonth'
    ];

    protected function getYearMonthAttribute()
    {
        return data_get($this, 'date')?->format('Y-m');
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

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id');
    }

    public function requests()
    {
        return $this->morphToMany(Request::class, 'requestable');
    }
}
