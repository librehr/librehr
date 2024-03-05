<?php

namespace App\Notifications;

use App\Filament\Pages\Dashboard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class Posts extends \Filament\Notifications\Notification
{
    use Queueable;

    protected $data;
    protected $recipient;

    public function __construct(string $id)
    {
        parent::__construct($id);
    }


    public function setData($recipient, $data)
    {
        $this->recipient = $recipient;
        $this->data = $data;
        return $this;
    }

    public function toMail()
    {
        $message = (new MailMessage)
            ->subject('New post from ' . config('app.name'))
            ->line(data_get($this->data, 'title'))
            ->action('Read full post', url(Dashboard::getNavigationUrl()))
            ->line('Thank you for using ' . config('app.name'));

        Mail::to($this->recipient)->send($message);
    }
}
