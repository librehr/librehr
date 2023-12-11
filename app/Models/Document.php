<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function absences()
    {
        return $this->morphedByMany(Absence::class, 'documentable');
    }

    public function contracts()
    {
        return $this->morphedByMany(Contract::class, 'documentable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'documentable');
    }
}
