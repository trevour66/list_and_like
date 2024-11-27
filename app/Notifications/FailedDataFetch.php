<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\IGAccessCodes;


class FailedDataFetch extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public IGAccessCodes $IGAccessCodes)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Data Sync Failed - {$this->IGAccessCodes->IG_USERNAME}")
            ->greeting("Hi")
            ->line('Unfortunately, the data sync process was unsuccessful. Please give it a go again!')
            ->line('If the problem persist, Please contact our support. We apologize for the inconvenience and appreciate your patience.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
