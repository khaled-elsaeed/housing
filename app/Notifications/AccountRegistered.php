<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Mail\AccountRegistered as AccountRegisteredMailable;

class AccountRegistered extends Notification implements ShouldQueue
{
    use Queueable;


    public function __construct(public User $user)
    {
        
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new AccountRegisteredMailable($this->user))
        ->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        
        return [

            'title' => 'Account created',
            'message' => 'Your has registered successfully.',
            'created_at' => now(),

        ];
    }

}