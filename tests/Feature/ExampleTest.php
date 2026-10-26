<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Unauthenticated access to '/' redirects to login
        $response = $this->get('/');
        $response->assertRedirect('/login');

        // Login page should return 200 OK
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }
}
