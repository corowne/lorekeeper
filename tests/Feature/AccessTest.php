<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test landing page access
     *
     * @return void
     */
    public function test_canAccessHomepage()
    {
        // Ensure 'about' is present to access, otherwise it will error
        $this->artisan('add-text-pages');

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
