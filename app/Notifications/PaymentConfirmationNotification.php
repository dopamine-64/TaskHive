<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Payment Confirmed')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your payment has been successfully confirmed.')
            ->action('View Payment Details', url('/bookings'))
            ->line('Thank you for choosing TaskHive.');
    }
}
