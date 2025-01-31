<?php

namespace App\Jobs;

use App\Mail\AccountCredentials;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ResetAccountCredentials implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Collection $users
    ) {}

    public function generateStrongPassword(int $length = 10): string
{
    // Define character sets
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $specialChars = '!@#?';

    // Ensure at least one character from each set
    $password = [
        $lowercase[random_int(0, strlen($lowercase) - 1)],
        $uppercase[random_int(0, strlen($uppercase) - 1)],
        $numbers[random_int(0, strlen($numbers) - 1)],
        $specialChars[random_int(0, strlen($specialChars) - 1)]
    ];

    // Fill remaining length with random characters from all sets
    $allChars = $lowercase . $uppercase . $numbers . $specialChars;
    for ($i = count($password); $i < $length; $i++) {
        $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
    }

    // Shuffle the password array
    shuffle($password);

    // Convert array to string
    return implode('', $password);
}

// Update in the job
public function handle(): void
{
    $this->users->each(function ($user) {
        try {
            // Use the new strong password generation method
            $password = $this->generateStrongPassword();
            
            $user->update([
                'password' => Hash::make($password),
            ]);

            // Pass the user object directly instead of creating a username string
            Mail::to($user->email)
                ->send(new AccountCredentials($user, $password));

          
        } catch (Throwable $e) {
            Log::error('Failed to reset password', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    });
}

    public function failed(Throwable $exception): void
    {
        Log::error('Account credentials job failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
