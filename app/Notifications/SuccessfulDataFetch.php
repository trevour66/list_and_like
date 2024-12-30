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


        // Jordan
        // List & Like

        return (new MailMessage)
            // ->subject("Data Sync Completed Successfully - {$this->IGAccessCodes->IG_USERNAME}")
            ->subject("Your List & Like Account is Ready!")
            ->greeting("Hooray!")
            ->line('We are pleased to share that your data syncing process is complete! Aka you will start to see your Dashboard, Community, Engagements and IG Profiles populated with your highest engaged folks.')
            ->line('You will receive an additional email with how to best move forward with adding more profiles from Webhooks or our Google Chrome extension.')
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
