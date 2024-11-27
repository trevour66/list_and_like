<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\IGAccessCodes;


class SuccessfulDataFetch extends Notification
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
            ->subject("Data Sync Completed Successfully - {$this->IGAccessCodes->IG_USERNAME}")
            ->greeting("Hurray")
            ->line('We are pleased to inform you that the data sync process has been completed successfully. All updates are now available, and your data is up to date.')
            ->line('If you have any questions or need further assistance, please do not hesitate to contact us.')
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
