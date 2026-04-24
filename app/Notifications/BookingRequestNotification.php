<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRequestNotification extends Notification
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
        $bookingDate = $this->tracking->booking_date ?? 'Not specified';
        $bookingTime = $this->tracking->booking_time ?? 'Not specified';

        return (new MailMessage())
            ->subject('New Booking Request Received')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have received a new booking request on TaskHive.")
            ->line("Booking ID: #{$bookingId}")
            ->line("Customer ID: #{$customerId}")
            ->line("Scheduled Date: {$bookingDate}")
            ->line("Scheduled Time: {$bookingTime}")
            ->action('Review Booking Request', url('/provider/profile'))
            ->line('Please review and respond from your provider dashboard.');
    }
}
