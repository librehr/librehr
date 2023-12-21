<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AbsenceType
 *
 * @property int $id
 * @property string $name
 * @property array|null $attributes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbsenceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AbsenceType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array'
    ];
}
