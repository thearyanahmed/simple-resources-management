<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\HtmlSnippet;
use App\Models\Link;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public array $adminAuthHeader = ['user_email' => 'admin@admin.com'];


    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        collect([
//                Link::class, HtmlSnippet::class,
  File::class,
            ])
            ->each(function ($model){
                $model::factory()->count(100)->create()->each(function ($link) use ($model) {
                    Resource::factory()->count(1)->create([
                        'resourceable_type' => $model,
                        'resourceable_id'   => $link->id
                    ]);
                });
            });
    }

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
            $response = $this->json($method,$endpoint,[],$this->adminAuthHeader);
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
                'opens_in_new_tab' => false,
            ],
            [
                'resource_type'   => 'link',
                'title'            => 'A test link resource that works with numeric booleans like 0.',
                'link'             => 'https://github.com/thearyanahmed',
                'opens_in_new_tab' => 0,
            ],
            [
                'resource_type'   => 'link',
                'title'            => 'A test link resource that works with numeric booleans like 1.',
                'link'             => 'https://github.com/thearyanahmed',
                'opens_in_new_tab' => 1,
            ],
        ];

        foreach($testCases as $testCaseData) {
            $response = $this->json('post',route('resources.store'),$testCaseData,$this->adminAuthHeader);

            $resourceShouldBe = [
                'title' => $testCaseData['title'],
                'type' => 'link',
                'link' => [
                    'link' => $testCaseData['link'], // quite messy
                    'opens_in_new_tab' => $testCaseData['opens_in_new_tab']
                ]
            ];

            $response
                ->assertJson([
                    'success' => true,
                    'message' => 'resource created successfully.',
                    'resource' => $resourceShouldBe
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'resource' => [
                        'title',
                        'type',
                        'link' => [
                            'link',
                            'opens_in_new_tab'
                        ],
                        'id',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertStatus(201);
        }

        $this->assertEquals($resourceCount + count($testCases), Resource::count());
        $this->assertEquals($linkCount + count($testCases), Link::count());
    }

    public function test_resource_type_is_required_when_creating_a_resource()
    {
        $data = [
            'title'            => 'A test link resource.',
            'link'             => 'https://thearyanahmed.com',
            'opens_in_new_tab' => true,
        ];

        $response = $this->json('post',route('resources.store'),$data,$this->adminAuthHeader);

        $response
            ->assertJson([
                'errors' => [
                    'resource_type' => ["The resource type field is required."]
                ]
            ])
            ->assertStatus(422);

        $data['resource_type'] = 'link';

        $response = $this->json('post',route('resources.store'),$data,$this->adminAuthHeader);

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

        $response = $this->json('post',route('resources.store'),$data,$this->adminAuthHeader);

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

        $response = $this->json('post',route('resources.store'),$data,$this->adminAuthHeader);

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
        $resourceCount = Resource::count();
        $linkCount = Link::count();

        $testCases = [
            ['data' => ['link' => 'https:\/invalidUrl', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'link' ], 'errors' => [ 'link' => ['The link must be a valid URL.']] ],
            ['data' => ['link' => 'https://averyveryveryverylongurlasdadasdasdasdasdasdadsasdasdasdasdaaasdadasdiahidhaiodaoidhioashdioahsdohaoudihaoshdioashdoihasiodhashsdioahdiohasiodhoashdoihaoidhioashdiasdjajsdiajdiajdsjajsdiasjdiajsdasidjaijsdaijdsiasjdiajsdajsdiajsdiajsdiajsdoahdioahodshaodhioashdoahsoidhaohdsouahsdouhasodhoasd.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link must not be greater than 255 characters.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => 'invalid boolean', 'resource_type' => 'link'], 'errors' => ['opens_in_new_tab' => ['The opens in new tab field must be true or false.']]],
            ['data' => ['link' => [], 'title' => [], 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link field is required.']],  ['title' => ['The title field is required.']]],
            ['data' => ['link' => null, 'title' => null, 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link field is required.']],['title' => ['The title field is required.']] ],
            ['data' => ['link' => "null", 'title' => "null", 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link must be a valid URL.']]],
            ['data' => ['link' => (object) [], 'title' => (object) [], 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link field is required.']]],
            ['data' => ['link' => UploadedFile::fake()->image('invalid.png'), 'title' => UploadedFile::fake()->image('invalid.png'), 'opens_in_new_tab' => true, 'resource_type' => 'link'], 'errors' => ['link' => ['The link must be a valid URL.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'file'], 'errors' => [ 'file' => ['The file field is required.']]],
            ['data' => ['link' => 'https://hello.com', 'title' => 'hello world', 'opens_in_new_tab' => true, 'resource_type' => 'html_snippet'], 'errors' => [ 'markup' => ['The markup field is required.'], 'description' => ['The description field is required.']]],
        ];

        foreach($testCases as $testCase) {
            $response = $this->json('post',route('resources.store'),$testCase['data'],$this->adminAuthHeader);

            $response
                ->assertJson(['errors' => $testCase['errors']])
                ->assertStatus(422);
        }

        $this->assertEquals($resourceCount,Resource::count());
        $this->assertEquals($linkCount,Link::count());
    }

    public function test_html_snippet_can_not_be_created_with_invalid_data()
    {
        $resourceCount = Resource::count();
        $htmlSnippetCount = HtmlSnippet::count();

        $testCases = [
            ['data' => ['description' => '', 'title' => '', 'markup' => '' ,'resource_type' => 'html_snippet' ], 'errors' => [ 'description' => ['The description field is required.'], 'title' => ['The title field is required.'],  'markup' => ['The markup field is required.']] ],
            ['data' => ['description' => UploadedFile::fake()->image('pseudo_image.png'),'markup' => UploadedFile::fake()->image('pseudo_image.png') , 'title' => UploadedFile::fake()->image('pseudo_image.png'),  'resource_type' => 'html_snippet'], 'errors' => [ 'description' => ['The description must be a string.'], 'title' => ['The title must be a string.'], 'markup' => ['The markup must be a string.']] ],
            ['data' => [ 'description' => $this->faker->realTextBetween(250,300) , 'markup' => $this->faker->randomHtml(1,1) , 'title' => $this->faker->realTextBetween(250,300) , 'resource_type' => 'html_snippet'], 'errors' => [ 'title' => ['The title must not be greater than 255 characters.'] , 'description' => ['The description must not be greater than 255 characters.'] ]],
            ['data' => ['description' => [], 'markup' => [], 'title' => [] , 'resource_type' => 'html_snippet'], 'errors' => [  'description' => ['The description field is required.'], 'title' => ['The title field is required.'], 'markup' => ['The markup field is required.']]],
            ['data' => ['description' => (object) [], 'markup' => (object)[], 'title' => (object)[] , 'resource_type' => 'html_snippet'], 'errors' => [ 'description' => ['The description field is required.'], 'title' => ['The title field is required.'], 'markup' => ['The markup field is required.']]],
            ['data' => ['description' => "demo description", 'markup' => $this->faker->randomHtml(1,1) , 'title' => "demo title" , 'resource_type' => 'file'], 'errors' => [ 'file' => ['The file field is required.']]],
            ['data' => ['description' => "demo description", 'markup' => $this->faker->randomHtml(1,1) , 'title' => "demo title" , 'resource_type' => 'link'], 'errors' => [ 'link' => ['The link field is required.']]],
        ];

        foreach($testCases as $testCaseData) {
            $response = $this->json('post',route('resources.store'),$testCaseData['data'],$this->adminAuthHeader);

            $response
                ->assertJson(['errors' => $testCaseData['errors']])
                ->assertStatus(422);
        }

        $this->assertEquals($resourceCount,Resource::count());
        $this->assertEquals($htmlSnippetCount,HtmlSnippet::count());
    }

    public function test_html_snippet_resource_can_be_created()
    {
        $resourceCount    = Resource::count();
        $htmlSnippetCount = HtmlSnippet::count();

        $rounds = mt_rand(5,50);

        foreach (range(1,$rounds) as $_) {
            $testData = [
                'title'         => $this->faker->realTextBetween(5,250),
                'description'   => $this->faker->realTextBetween(5,250),
                'markup'        => str_replace("\n","",$this->faker->randomHtml(1,1)),
                'resource_type' => 'html_snippet'
            ];

            $response = $this->json('post',route('resources.store'),$testData,$this->adminAuthHeader);

            $resourceShouldBe = [
                'title' => $testData['title'],
                'type' => 'html_snippet',
                'html_snippet' => [
                    'description' => $testData['description'],
                    'markup'      => $testData['markup']
                ]
            ];

            $response
                ->assertJson([
                    'success' => true,
                    'message' => 'resource created successfully.',
                    'resource' => $resourceShouldBe
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'resource' => [
                        'title',
                        'type',
                        'html_snippet' => [
                            'description',
                            'markup'
                        ],
                        'id',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertStatus(201);
        }

        $this->assertEquals($resourceCount + $rounds, Resource::count());
        $this->assertEquals($htmlSnippetCount + $rounds, HtmlSnippet::count());
    }

    public function test_file_resource_can_be_created()
    {
        $resourceCount = Resource::count();
        $fileCount     = File::count();

        $rounds = mt_rand(2, 3);

        foreach (range(1, $rounds) as $_) {
            $testData = [
                'title' => $this->faker->realTextBetween(5, 250),
                'resource_type' => 'file',
                'file' => UploadedFile::fake()->image('test.pdf')
            ];

            $response = $this->json('post',route('resources.store'),$testData,$this->adminAuthHeader);

            $resourceShouldBe = [
                'title' => $testData['title'],
                'type' => 'file',
                'file' => [] // file abs_path being checked with assertFileExists
            ];

            $response
                ->assertJson([
                    'success'  => true,
                    'message'  => 'resource created successfully.',
                    'resource' => $resourceShouldBe
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'resource' => [
                        'title',
                        'type',
                        'file' => [
                            'abs_url',
                            'path'
                        ],
                        'id',
                        'created_at',
                        'updated_at'
                    ],
                ])
                ->assertStatus(201);

            $this->assertTrue(Storage::exists($response->decodeResponseJson()['resource']['file']['path']));
        }

        $this->assertEquals($resourceCount + $rounds, Resource::count());
        $this->assertEquals($fileCount + $rounds, File::count());
    }

    public function test_a_resource_can_be_deleted()
    {
        $resourceCount = Resource::count();

        $resources = Resource::with('resourceable')->inRandomOrder()->take(10)->get();

        $resources->each(function ($resource) use (&$resourceCount) {
            $response = $this->json('delete',route('resources.delete',$resource->id),[],$this->adminAuthHeader);

            $response
                ->assertJson([
                    'success' => true,
                    'message' => 'resource deleted successfully.'
                ])
                ->assertStatus(200);

            $this->assertEquals($resourceCount - 1,Resource::count());

            $this->assertModelMissing($resource);
            $this->assertModelMissing($resource->resourceable);

            $resourceCount = $resourceCount - 1;

            if($resource->type === Resource::RESOURCE_FILE) {
                $this->assertFalse(Storage::exists($resource->path));
            }
        });
    }

    public function test_delete_route_is_only_accessible_by_admin()
    {
        $resource = Resource::factory()->count(1)->create([
            'resourceable_type' => Link::class, // pseudo
            'resourceable_id'   => mt_rand(1000,100000)
        ]);

        $response = $this->json('delete',route('resources.delete',$resource[0]->id),[],[]);

        $response
            ->assertJson([
                'error' => 'unauthorized.'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_a_resource_can_be_viewed()
    {

    }

    public function test_a_resource_can_be_viewed_by_anyone()
    {
        $resource = Resource::first();

        $response = $this->json('get',route('resources.show',$resource->id),[],[]);

        $response->dump();

        $response
            ->assertJsonStructure([
                'title',
                'id',
                'type'
            ])
            ->assertStatus(200);
    }

}
