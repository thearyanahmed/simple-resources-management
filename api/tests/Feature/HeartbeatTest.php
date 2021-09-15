<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeartbeatTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/heartbeat');

        $response->assertJson([
            'status' => 'beating'
        ],true);

        $response->assertStatus(200);
    }
}
