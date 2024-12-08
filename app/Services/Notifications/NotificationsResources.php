<?php

namespace App\Services\Notifications;

use App\Services\BaseService;

abstract class NotificationsResources extends BaseService
{
    public function __construct(protected $data)
    {
    }

    abstract public function getTitle(): string;
    abstract public function getDescription(): string;
    abstract public function getUrl(): string;
}
