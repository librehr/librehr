<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Requests extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
            protected $subject,
            protected $body,
            protected $action = null,
            protected $url = null
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase()
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
            'action' => $this->action,
            'url' => $this->url,
        ];
    }


    public function toMail($notifiable)
    {
        $mail = (new MailMessage)->subject($this->subject)->line($this->body);

        if ($this->action !== null && $this->url !== null) {
            $mail = $mail->action($this->action, url($this->url));
        }

        return $mail;
    }
}
