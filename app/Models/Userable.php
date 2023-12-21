<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Userable
 *
 * @property int $id
 * @property int $user_id
 * @property int $userable_id
 * @property string $userable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $supervisors
 * @property-read int|null $supervisors_count
 * @method static \Illuminate\Database\Eloquent\Builder|Userable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Userable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Userable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereUserableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Userable whereUserableType($value)
 * @mixin \Eloquent
 */
class Userable extends Model
{
    use HasFactory;

    public function supervisors(): MorphToMany
    {
        return $this->morphedByMany(Contract::class, 'userable');
    }
}
