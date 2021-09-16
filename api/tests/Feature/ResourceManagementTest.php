<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    public $authHeader = ['user_email' => 'admin@admin.com'];

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

        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint,[],$this->authHeader);
            $response->assertStatus(200);
        }
    }

    public function test_link_resource_can_be_created()
    {
        $resourceCount = Resource::count();
        $linkCount     = Link::count();

        // use loop
        $data = [
            'resource_type'   => 'link',
            'title'            => 'A test link resource.',
            'link'             => 'https://thearyanahmed.com',
            'opens_in_new_tab' => true,
        ];

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response
            ->assertJson([
                'success' => true,
                'message' => 'resource created successfully.'
            ])
            ->assertStatus(201);

        $this->assertEquals($resourceCount + 1, Resource::count());
        $this->assertEquals($linkCount + 1, Link::count());
    }

    public function test_resource_type_is_required_when_creating_a_resource()
    {
        $data = [
            'title'            => 'A test link resource.',
            'link'             => 'https://thearyanahmed.com',
            'opens_in_new_tab' => true,
        ];

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response->dump();
        $response
            ->assertJson([
                'errors' => [
                    'resource_type' => ["The resource type field is required."]
                ]
            ])
            ->assertStatus(422);
    }
}
