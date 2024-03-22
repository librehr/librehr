<?php

return [
    'allowed_notifications' => [
        \App\Services\Notifications\Resources\Post::class       => ['mail', 'web'],
        \App\Services\Notifications\Resources\Document::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\ContractToolDelivered::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\AttendanceValidation::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\AttendanceRevision::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TimeOffDenied::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TimeOffValidated::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TimeOffRequest::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\ReactionAdded::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TaskAssigned::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TaskUnassigned::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TaskActivityAdded::class   => ['mail', 'web'],
        \App\Services\Notifications\Resources\TaskPriorityChanged::class   => ['mail', 'web'],
    ]
];
