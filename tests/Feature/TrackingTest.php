<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_enable()
    {
        $response = $this->get('/tracking');

        $response->assertStatus(200);
    }
}
