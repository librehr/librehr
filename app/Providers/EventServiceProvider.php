<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\ContractTool;
use App\Models\Document;
use App\Models\Documentable;
use App\Models\Post;
use App\Models\Task;
use App\Models\Team;
use App\Models\Userable;
use App\Observers\ContractObserver;
use App\Observers\ContractToolObserver;
use App\Observers\DocumentObserver;
use App\Observers\PostObserver;
use App\Observers\TaskObserver;
use App\Observers\TeamObserver;
use App\Observers\UserableObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Contract::observe(ContractObserver::class);
        Team::observe(TeamObserver::class);
        Post::observe(PostObserver::class);
        Document::observe(DocumentObserver::class);
        ContractTool::observe(ContractToolObserver::class);
        Userable::observe(UserableObserver::class);
        Task::observe(TaskObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
