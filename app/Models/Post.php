<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use App\Models\Trait\TeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, TeamScope;

    protected $guarded = [];
}
