<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    public function supervisors(): MorphToMany
    {
        return $this->morphedByMany(Contract::class, 'userable');
    }

    public function taskUsers(): MorphToMany
    {
        return $this->morphedByMany(Task::class, 'userable');
    }
}
