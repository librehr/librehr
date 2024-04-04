<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TasksCategory extends Model
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
        'attributes' => 'array'
    ];

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(TasksCategory::class, 'parent_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
