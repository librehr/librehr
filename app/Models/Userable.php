<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Userable extends Model
{
    use HasFactory;

    public function supervisors(): MorphToMany
    {
        return $this->morphedByMany(Contract::class, 'userable');
    }
}
