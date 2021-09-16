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

        $testCases = [
            [
                'resource_type'   => 'link',
                'title'            => 'A test link resource.',
                'link'             => 'https://thearyanahmed.com',
                'opens_in_new_tab' => true,
            ],
            [
                'resource_type'   => 'link',
                'title'            => 'A test link resource that opens in the same tab.',
                'link'             => 'https://github.com/thearyanahmed',
                'opens_in_new_tab' => false, // opens in new tab
            ],
        ];

        foreach($testCases as $i => $testCaseData) {
            $response = $this->json('post',route('resources.store'),$testCaseData['data'],$this->authHeader);

            unset($testCaseData['resource_type']);

            $testCaseData['link'] = [
                'link' => $testCaseData['link'],
                'opens_in_new_tab' => $testCaseData['opens_in_new_tab']
            ];

            $response
                ->assertJson([
                    'success' => true,
                    'message' => 'resource created successfully.',
                    'resource' => [
                        $testCaseData,
                    ]
                ])
                ->assertStatus(201);

            $this->assertEquals($resourceCount + $i, Resource::count());
            $this->assertEquals($linkCount + $i, Link::count());
        }


    }

    public function test_resource_type_is_required_when_creating_a_resource()
    {
        $data = [
            'title'            => 'A test link resource.',
            'link'             => 'https://thearyanahmed.com',
            'opens_in_new_tab' => true,
        ];

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response
            ->assertJson([
                'errors' => [
                    'resource_type' => ["The resource type field is required."]
                ]
            ])
            ->assertStatus(422);

        $data['resource_type'] = 'link';

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response->assertStatus(201);
    }

    public function test_resource_creation_returns_error_with_invalid_data()
    {
        // title, link with empty string should not be created
        $data = [
            'title'            => '',
            'link'             => '',
            'opens_in_new_tab' => true,
            'resource_type'    => 'link',
        ];

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response
            ->assertJson([
                'errors' => [
                    'link' => ["The link field is required."],
                    'title' => ["The title field is required."],
                ]
            ])
            ->assertStatus(422);

        // a link that is not valid should not be created
        $data['link'] = 'invalid-link';
        $data['title'] = 'hello world';

        $response = $this->json('post',route('resources.store'),$data,$this->authHeader);

        $response
            ->assertJson([
                'errors' => [
                    'link' => ['The link must be a valid URL.']
                ]
            ])
            ->assertStatus(422);
    }

    public function test_links_resource_creation_fails_with_invalid_data()
    {
        $testCases = [
            ['data' => ['link' => 'https:\/invalidUrl', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'link' ], 'errors' => [ 'link' => ['The link must be a valid URL.']] ],
            ['data' => ['link' => 'https://averyveryveryverylongurlasdadasdasdasdasdasdadsasdasdasdasdaaasdadasdiahidhaiodaoidhioashdioahsdohaoudihaoshdioashdoihasiodhashsdioahdiohasiodhoashdoihaoidhioashdiasdjajsdiajdiajdsjajsdiasjdiajsdasidjaijsdaijdsiasjdiajsdajsdiajsdiajsdiajsdoahdioahodshaodhioashdoahsoidhaohdsouahsdouhasodhoasd.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link must not be greater than 250 characters.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => 'invalid boolean', 'resource_type' => 'link'], 'errors' => ['opens_in_new_tab' => ['The opens in new tab field must be true or false.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'pdf'], 'errors' => [ 'file' => ['The file field is required.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'html_snippet'], 'errors' => [ 'markup' => ['The markup field is required.'], 'description' => ['The description field is required.']]],
        ];

        foreach($testCases as $testCase) {
            $response = $this->json('post',route('resources.store'),$testCase['data'],$this->authHeader);

            $response
                ->assertJson(['errors' => $testCase['errors']])
                ->assertStatus(422);
        }
    }

}
