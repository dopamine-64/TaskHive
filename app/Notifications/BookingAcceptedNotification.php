<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAcceptedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Booking Accepted')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Great news! Your booking request has been accepted.')
            ->action('View Booking Details', url('/bookings'))
            ->line('Thank you for using TaskHive.');
    }
}
