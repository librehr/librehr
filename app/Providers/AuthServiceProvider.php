<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Business;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Policies\BusinessPolicy;
use App\Policies\ContractPolicy;
use App\Policies\ContractTypePolicy;
use App\Policies\RolePolicy;
use App\Policies\TeamPolicy;
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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
