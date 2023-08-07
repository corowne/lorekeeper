<?php

namespace Tests\Feature;

<<<<<<< HEAD:tests/Feature/AccessTest.php
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

=======
use Tests\TestCase;

class ExampleTest extends TestCase {
    /**
     * A basic test example.
     */
    public function testBasicTest() {
>>>>>>> develop:tests/Feature/ExampleTest.php
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
