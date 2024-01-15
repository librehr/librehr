<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array'
    ];

    protected $guarded = [];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
