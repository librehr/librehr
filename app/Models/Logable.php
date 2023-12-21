<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Logable
 *
 * @property int $id
 * @property int $user_id
 * @property int $logable_id
 * @property string $logable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Logable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereLogableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereLogableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logable whereUserId($value)
 * @mixin \Eloquent
 */
class Logable extends Model
{
    use HasFactory;

    protected $guarded = [];
}
