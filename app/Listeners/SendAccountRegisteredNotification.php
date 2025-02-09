<?php

namespace App\Listeners;

use App\Events\AccountRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\AccountRegistered as AccountRegisteredNotification; 
use Illuminate\Support\Facades\Log;

class SendAccountRegisteredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // Initialization code if needed
    }

    /**
     * Handle the event.
     */
    public function handle(AccountRegistered $event): void
    {
        try {
            $user = $event->getUser();
            if ($user) {
                $user->notify(new AccountRegisteredNotification($user));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send account registration notification', [
                'error'   => $e->getMessage(),
                'user_id' => $user->id ?? null,
            ]);
        }
    }
}
