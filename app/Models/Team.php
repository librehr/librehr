<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    protected $guarded = [];

    public function contracts()
    {
        return $this->hasMany(Contract::class)->activeContracts();
    }

    public function supervisors()
    {
        return$this->morphToMany(User::class, 'userable');
    }
}
