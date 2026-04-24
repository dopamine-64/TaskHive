<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingAcceptedConfirmation extends Notification
{
    use Queueable;

    protected $tracking;

    public function __construct($tracking)
    {
        $this->tracking = $tracking;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $bookingId = $this->tracking->id ?? 'N/A';
        $customerId = $this->tracking->customer_id ?? 'N/A';

        return (new MailMessage)
            ->subject('Booking Accepted Confirmation')
            ->greeting("Hello {$notifiable->name}!")
            ->line('This is a confirmation that you accepted a booking request.')
            ->line("Booking ID: #{$bookingId}")
            ->line("Customer ID: #{$customerId}")
            ->action('View Active Jobs', url('/provider/profile'))
            ->line('Thank you for delivering with TaskHive.');
    }
}
