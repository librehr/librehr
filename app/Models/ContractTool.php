<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTool extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }
}
