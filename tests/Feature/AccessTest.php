<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase {
    use RefreshDatabase;

    /**
     * Test landing page access.
     */
    public function testCanAccessHomepage() {
        // Ensure 'about' is present to access, otherwise it will error
        $this->artisan('add-text-pages');

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
