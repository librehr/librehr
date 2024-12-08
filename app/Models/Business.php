<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Business
 *
 * @property int $id
 * @property string $name
 * @property int $active
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Business newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Business query()
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereUuid($value)
 * @mixin \Eloquent
 */
class Business extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array'
    ];

    protected $guarded = [];

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
