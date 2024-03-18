<?php

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope());
    }

    protected $casts = [
        'attributes' => 'array',
        'start' => 'date',
        'status' => TaskStatusEnum::class,
        'priority' => TaskPriorityEnum::class,
    ];

    protected $guarded = [];



    public function observers()
    {
        return $this->morphToMany(User::class, 'userable');
    }

    public function requests()
    {
        return $this->morphToMany(Request::class, 'requestable');
    }

    public function contracts()
    {
        return $this->morphToMany(Contract::class, 'contratable');
    }

    public function tasksCategory()
    {
        return $this->belongsTo(TasksCategory::class);
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
}
