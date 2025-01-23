<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendUserWelocmeEmail implements ShouldQueue
{
    use Queueable;

    private $username;

    /**
     * Create a new job instance.
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
