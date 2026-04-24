<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolutionNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Dispute Resolution Update')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your dispute case has been reviewed and updated.')
            ->action('View Dispute Details', url('/bookings'))
            ->line('Please check the latest status in your TaskHive account.');
    }
}
