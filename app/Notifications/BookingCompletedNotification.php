<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCompletedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Booking Completed')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your booking has been marked as completed.')
            ->action('View Completed Booking', url('/bookings'))
            ->line('We hope your experience with TaskHive was great.');
    }
}
