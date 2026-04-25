<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $tracking;
    protected $amount;

    public function __construct($tracking, $amount = null)
    {
        $this->tracking = $tracking;
        $this->amount = $amount;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $amount = number_format((float) ($this->amount ?? $this->tracking->amount ?? 0), 2);
        $bookingId = $this->tracking->id ?? 'N/A';

        return (new MailMessage)
            ->subject('Payment Received Confirmation')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A customer payment has been successfully received for your job.")
            ->line("Booking ID: #{$bookingId}")
            ->line("Amount Received: BDT {$amount}")
            ->action('View Provider Dashboard', url('/provider/profile'))
            ->line('Thank you for continuing to serve on TaskHive.');
    }
}
