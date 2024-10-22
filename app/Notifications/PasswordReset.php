<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PasswordReset extends Notification
{
    use Queueable;

    protected $user;
    protected $token; // Add a token property

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token; // Store the token
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset')
            ->view('emails.password_reset', [
                'username' => $this->user->username,
                'resetUrl' => $this->generateResetUrl() // Call the method without passing token
            ]);
    }

    public function generateResetUrl()
    {
        return url('/password/reset/' . $this->token); // Use the stored token
    }
}
