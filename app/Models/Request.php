<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function absences()
    {
        return $this->morphedByMany(Absence::class, 'requestable');
    }
}
