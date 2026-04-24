<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingCompletedConfirmation extends Notification
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
            ->subject('Job Completion Confirmation')
            ->greeting("Hello {$notifiable->name}!")
            ->line('This confirms that you marked a job as completed.')
            ->line("Booking ID: #{$bookingId}")
            ->line("Customer ID: #{$customerId}")
            ->action('View Job History', url('/provider/profile'))
            ->line('Thank you for your service on TaskHive.');
    }
}
