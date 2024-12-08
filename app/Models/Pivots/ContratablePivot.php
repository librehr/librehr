<?php

namespace App\Models\Pivots;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ContratablePivot extends MorphPivot
{
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contratable()
    {
        return $this->morphTo();
    }
}
