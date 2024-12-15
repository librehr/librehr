<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requestable extends Model
{
    use HasFactory;

    protected $with = [
        'userTo',
        'contract',
    ];

    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function userTo()
    {
        return $this->belongsTo(User::class);
    }

    public function requestable()
    {
        return $this->morphTo();
    }
}
