<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailNotification;
use App\Services\Notifications\NotificationsResources;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class Notifications extends BaseService
{
    /**
     * @param string $notification
     * @param $record
     * @param array|int|null $usersId
     * @return void
     */
    public static function notify(
        string $notification,
        $record,
        array|int|null $usersId = null,
    ): void
    {
        $allowedNotifications = config('notifications.allowed_notifications');
        $users = User::query()->where('active', true);

        if ($usersId !== null) {
            $users = $users->whereIn('id', (is_int($usersId) ? [$usersId] : $usersId));
        }

        $users->chunkById(200, function ($users) use ($allowedNotifications, $notification, $record) {
                foreach ($users as $user) {
                    $result = self::getNotificationData($notification, $record);

                    if (in_array('mail', data_get($allowedNotifications, $notification, []))) {
                        $user->notify(new EmailNotification(
                            $result
                        ));
                    }

                    if (in_array('web', data_get($allowedNotifications, $notification, []))) {
                        $user->notify(
                            Notification::make()
                                ->title(data_get($result, 'title'))
                                ->body(data_get($result, 'description'))
                                ->actions([
                                    Action::make('open')
                                    ->url(data_get($result, 'url'))
                                ])
                                ->toBroadcast(),
                        );
                    }
                }
            });
    }

    /**
     * @param $notification
     * @param $data
     * @return array
     */
    protected static function getNotificationData($notification, $data): array
    {
        $class = new $notification($data);
        if ($class instanceof NotificationsResources) {
            return [
                'title' => $class->getTitle(),
                'description' => $class->getDescription(),
                'url' => $class->getUrl(),
            ];
        }

        return [];
    }
}
