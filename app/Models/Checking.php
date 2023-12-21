<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Checking
 *
 * @property int $id
 * @property string $date
 * @property string $start
 * @property string|null $end
 * @property int $contract_id
 * @property int|null $validated_by
 * @property string|null $validated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Checking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Checking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Checking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereValidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Checking whereValidatedBy($value)
 * @mixin \Eloquent
 */
class Checking extends Model
{
    use HasFactory;
}
