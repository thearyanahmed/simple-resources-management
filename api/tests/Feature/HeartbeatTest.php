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
    public function test_app_is_beating()
    {
        $response = $this->get('/api/heartbeat');

        $response
            ->assertJson(['status' => 'beating'],true)
            ->assertStatus(200);
    }
}
