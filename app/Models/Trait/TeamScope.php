<?php

namespace App\Models\Trait;

use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TeamScope
{
    protected static function booted(): void
    {
        static::addGlobalScope('business', function (Builder $query) {
            $user = auth();
            if ($user->check() && data_get($user, 'businesses')) {
                $query->whereBelongsTo(auth()->user()->businesses);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
