<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivation extends Notification implements ShouldQueue {
    use Queueable;

    protected $user;

        public function __construct($user)
    {
        $this->user = $user;
    }

        public function via(object $notifiable): array
    {
        return ['mail'];
    }

        public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Activation')
            ->view('emails.account_activation', [
                'username' => $this->user->username,
                'activationUrl' => $this->generateActivationUrl($this->user->activation_token)             ]);
    }

        public function toArray(object $notifiable): array
    {
        return [
                    ];
    }

    public function generateActivationUrl($activationCode)     {
        return url('activate-account/' . $activationCode);     }
}
