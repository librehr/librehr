<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Notifications\DatabaseNotification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $with = [
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'attributes' => 'array'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getActiveBusinessId()
    {
        return data_get($this->getActiveBusiness(), 'id');
    }

    public function getActiveBusiness($uuid = null)
    {
        if ($uuid === null) {
            $uuid = data_get($this, 'attributes.default_business');
        }
        return Business::query()
            ->where('active', true)
            ->where('uuid', $uuid)->first();
    }

    public function getActiveContractId()
    {
        return data_get($this->getActiveContract(), 'id');
    }

    public function getActiveContract()
    {
        return $this->contracts()->ActiveContracts()->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }

    public function absences()
    {
        return $this->hasManyThrough(Absence::class, Contract::class);
    }
}
