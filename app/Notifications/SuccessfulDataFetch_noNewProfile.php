<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\IGAccessCodes;


class SuccessfulDataFetch_noNewProfile extends Notification
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


        // Jordan
        // List & Like

        return (new MailMessage)
            // ->subject("Data Sync Completed Successfully - {$this->IGAccessCodes->IG_USERNAME}")
            ->subject("Data Sync Completed - No Eligible Activity Detected in Your Recent Instagram Data")
            ->greeting("Hello")
            ->line('Your data syncing process is complete! However, the system did not pickup anynew data.')
            ->line("Currently, our system analyzes data from the past 3 months. It seems there were either no new posts from you during this period, or the system couldn't identify any Instagram profiles that have commented on at least five of your posts. If you believe this is incorrect, please don't hesitate to contact support for assistance.")
            ->line('Happy engaging!');
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
