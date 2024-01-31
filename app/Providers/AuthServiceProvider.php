<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\Business;
use App\Models\Calendar;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Place;
use App\Models\Planning;
use App\Models\Post;
use App\Models\Role;
use App\Models\Room;
use App\Models\Team;
use App\Models\Tool;
use App\Models\User;
use App\Policies\AbsencePolicy;
use App\Policies\AbsenceTypePolicy;
use App\Policies\BusinessPolicy;
use App\Policies\CalendarPolicy;
use App\Policies\ContractPolicy;
use App\Policies\ContractTypePolicy;
use App\Policies\PlacePolicy;
use App\Policies\PlanningPolicy;
use App\Policies\PostPolicy;
use App\Policies\RolePolicy;
use App\Policies\RoomPolicy;
use App\Policies\TeamPolicy;
use App\Policies\ToolPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        ContractType::class => ContractTypePolicy::class,
        Business::class => BusinessPolicy::class,
        Contract::class => ContractPolicy::class,
        Team::class => TeamPolicy::class,
        Absence::class => AbsencePolicy::class,
        AbsenceType::class => AbsenceTypePolicy::class,
        Place::class => PlacePolicy::class,
        Planning::class => PlanningPolicy::class,
        Post::class => PostPolicy::class,
        Calendar::class => CalendarPolicy::class,
        Tool::class => ToolPolicy::class,
        Room::class => RoomPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
