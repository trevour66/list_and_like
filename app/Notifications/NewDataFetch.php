<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\IGAccessCodes;


class NewDataFetch extends Notification
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
            ->subject("New Data Sync from - {$this->IGAccessCodes->IG_USERNAME}")
            ->greeting("Hurray")
            ->line('We wanted to let you know that the data sync process has started.')
            ->line('Our system is currently working to synchronize the latest updates, and you will be notified once the process is complete. If you have any questions or concerns, feel free to reach out.')
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
