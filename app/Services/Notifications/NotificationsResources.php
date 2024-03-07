<?php

namespace App\Services\Notifications;


use App\Models\Attendance;
use App\Models\AttendanceValidation;
use App\Models\User;
use App\Notifications\EmailNotification;
use App\Notifications\Posts;
use App\Services\BaseService;
use Carbon\Carbon;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

abstract class NotificationsResources extends BaseService
{
    public function __construct(protected $data) {}

    public abstract function getTitle(): string;
    public abstract function getDescription(): string;
    public abstract function getUrl(): string;
}
