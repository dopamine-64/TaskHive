<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to TaskHive!')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Thank you for registering with TaskHive.')
            ->line('You can now book services or offer your skills.')
            ->action('Explore TaskHive', url('/dashboard'))
            ->line('Thank you for joining our community!');
    }
}