<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_can_not_be_accessed_unless_user_is_admin()
    {
        // todo: maybe use named routes?
        $routeMap = [
            'get' => '/api/resources'
        ];

        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint);
            $response->assertStatus(401);
        }
    }

    public function test_management_routes_can_be_accessed_when_user_is_admin()
    {
        $routeMap = [
            'get' => '/api/resources'
        ];

        $authHeader = ['user_email' => 'admin@admin.com'];

        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint,$authHeader);
            $response->assertStatus(401);
        }
    }
}
