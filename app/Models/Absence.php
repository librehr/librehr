<?php

namespace App\Models;

use App\Enums\AbsenceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status_at' => 'datetime',
        'start' => 'date',
        'end' => 'date',
        'status' => AbsenceStatusEnum::class
    ];

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }
}
