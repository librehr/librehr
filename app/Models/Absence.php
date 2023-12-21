<?php

namespace App\Models;

use App\Enums\AbsenceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Absence
 *
 * @property int $id
 * @property int $absence_type_id
 * @property int $contract_id
 * @property string $comments
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon $end
 * @property AbsenceStatusEnum $status
 * @property int|null $status_by
 * @property \Illuminate\Support\Carbon|null $status_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AbsenceType $absenceType
 * @property-read \App\Models\Contract $contract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @method static \Illuminate\Database\Eloquent\Builder|Absence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Absence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Absence query()
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereAbsenceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereStatusAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereStatusBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Absence whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
