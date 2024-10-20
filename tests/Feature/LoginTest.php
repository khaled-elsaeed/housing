<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginTest extends TestCase
{


    /** @test */
    public function it_takes_less_than_two_seconds_to_login()
    {
        

        // Start time measurement
        $startTime = microtime(true);

        // Perform login
        $response = $this->post('/login', [
            'email' => 'khaled@gmail.com',
            'password' => 'passworda',
        ]);

        // End time measurement
        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;

        // Assert the response status is 302 (redirect after login)
        $response->assertStatus(302);

        // Check that the time taken is less than 2 seconds
        $this->assertLessThan(2, $timeTaken, "Login took too long: {$timeTaken} seconds");
    }
}
