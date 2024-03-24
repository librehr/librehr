<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Notifications\DatabaseNotification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property int $role_id
 * @property string $email
 * @property array|null $attributes
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Absence> $absences
 * @property-read int|null $absences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'active'
    ];

    protected $with = [
        'role',
    ];

    protected $appends = [
        'isAdmin'
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
        'attributes' => 'array',
        'active' => 'boolean',
    ];

    public function getIsAdminAttribute()
    {
        return data_get($this, 'role.name') == 'admin';
    }

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
        return \Cache::remember('business_' . $uuid, 3600, function () use ($uuid) {
            return Business::query()
                ->where('active', true)
                ->where('uuid', $uuid)->first();
        });
    }

    public function getActiveContractTeamSupervisors() {
        $contract = $this->getActiveContract()->load('team.supervisors');
        return data_get($contract, 'team.supervisors');
    }

    public function getActiveContractId()
    {
        return data_get($this->getActiveContract(), 'id');
    }

    // todo: need cache
    public function getActiveContract()
    {
        return Cache::remember('active_contract' . $this->id, 3600, function () {
            return $this->contracts()->ActiveContracts()->first();
        });
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

    public function tools()
    {
        return $this->hasManyThrough(ContractTool::class, Contract::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Contract::class);
    }

    public function plannings()
    {
        return $this->hasManyThrough(Planning::class, Contract::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = data_get($this, 'attributes.avatar');

        if ($avatar !== null) {
            $avatar = \Storage::url($avatar);
        }

        return $avatar;
    }
}
