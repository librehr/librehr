<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $casts = [
        'attributes' => 'array',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function supervisors()
    {
        return $this->morphToMany(User::class, 'userable', 'userables', 'userable_id', 'userable_id');
    }

    public function scopeActiveContracts($builder)
    {
        $builder->whereNull('end')->orWhere('end', '>', now());
    }
}
