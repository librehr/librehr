<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Contract
 *
 * @property int $id
 * @property int $contract_type_id
 * @property int $business_id
 * @property int $user_id
 * @property int $team_id
 * @property array|null $attributes
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \App\Models\ContractType $contractType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $supervisors
 * @property-read int|null $supervisors_count
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Contract activeContracts()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUserId($value)
 * @mixin \Eloquent
 */
class Contract extends Model
{
    use HasFactory;
    protected $casts = [
        'attributes' => 'array',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function supervisors()
    {
        return $this->morphToMany(User::class, 'userable', 'userables', 'userable_id', 'userable_id');
    }

    public function scopeActiveContracts($builder)
    {
        $builder->whereNull('end')->orWhere('end', '>', now());
    }

    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

    public function tools()
    {
        return $this->hasMany(ContractTool::class);
    }
}
