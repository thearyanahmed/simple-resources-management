<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Models\{File, Link, HtmlSnippet, Resource};
use Illuminate\Foundation\Testing\{RefreshDatabase,WithFaker};

class ResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public array $adminAuthHeader = ['user_email' => 'admin@admin.com'];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        collect([
                Link::class, HtmlSnippet::class, File::class,
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
            'post' => route('resources.store')
        ];

        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint);
            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        }

        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint,[],['user_email' => 'a_non_admin_email@hello.com']);
            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        }

        // with authentication header
        foreach($routeMap as $method => $endpoint) {
            $response = $this->json($method,$endpoint,[],$this->adminAuthHeader);
            $this->assertNotEquals(Response::HTTP_UNAUTHORIZED,$response->status());
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
                ->assertStatus(Response::HTTP_CREATED);
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
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $data['resource_type'] = 'link';

        $response = $this->json('post',route('resources.store'),$data,$this->adminAuthHeader);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_links_resource_creation_fails_with_invalid_data()
    {
        $resourceCount = Resource::count();
        $linkCount = Link::count();

        $testCases = [
            ['data' => ['link' => '', 'title' => '', 'opens_in_new_tab' => true, 'resource_type' => 'link' ], 'errors' => [ 'title' => ['The title field is required.'],'link' => ['The link field is required.']]],
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
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
            ['data' => [ 'description' => $this->faker->realText(mt_rand(260,400)) , 'markup' => $this->faker->randomHtml(1,1) , 'title' => $this->faker->realText(mt_rand(260,400)) , 'resource_type' => 'html_snippet'], 'errors' => [ 'title' => ['The title must not be greater than 255 characters.'] , 'description' => ['The description must not be greater than 255 characters.'] ]],
            ['data' => ['description' => [], 'markup' => [], 'title' => [] , 'resource_type' => 'html_snippet'], 'errors' => [  'description' => ['The description field is required.'], 'title' => ['The title field is required.'], 'markup' => ['The markup field is required.']]],
            ['data' => ['description' => (object) [], 'markup' => (object)[], 'title' => (object)[] , 'resource_type' => 'html_snippet'], 'errors' => [ 'description' => ['The description field is required.'], 'title' => ['The title field is required.'], 'markup' => ['The markup field is required.']]],
            ['data' => ['description' => "demo description", 'markup' => $this->faker->randomHtml(1,1) , 'title' => "demo title" , 'resource_type' => 'file'], 'errors' => [ 'file' => ['The file field is required.']]],
            ['data' => ['description' => "demo description", 'markup' => $this->faker->randomHtml(1,1) , 'title' => "demo title" , 'resource_type' => 'link'], 'errors' => [ 'link' => ['The link field is required.']]],
        ];

        foreach($testCases as $testCaseData) {
            $response = $this->json('post',route('resources.store'),$testCaseData['data'],$this->adminAuthHeader);

            $response
                ->assertJson(['errors' => $testCaseData['errors']])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
                'title'         => $this->faker->realText(mt_rand(10,40)),
                'description'   => $this->faker->realText(mt_rand(10,40)),
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
                'title' => $this->faker->realText(mt_rand(10,40)),
                'resource_type' => 'file',
                'file' => UploadedFile::fake()->create('test.pdf')
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
                ->assertStatus(Response::HTTP_OK);

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
                'errors' => ['authentication' => ['unauthorized.']]
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_a_resource_can_be_viewed()
    {
        $resource = Resource::first();

        $response = $this->json('get',route('resources.show',$resource->id),[],[]);

        $response
            ->assertJsonStructure([
                'title',
                'id',
                'type'
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_it_returns_a_404_when_non_existent_or_invalid_resource_id_is_requested()
    {
        $invalidIds = [
            '\r\n',
            '-',
            '+',
            UploadedFile::fake()->create('invalid_file.png'),
            '!@#',
            'invalid id',
            '/[0-9]+[a-z0-9]?$/',
            Resource::count() + mt_rand(10,1000)
        ];

        foreach ($invalidIds as $id) {
            $response = $this->json('get',route('resources.show',$id),[],[]);

            $response->assertStatus(Response::HTTP_NOT_FOUND);
        }
    }

    public function test_a_resource_can_be_viewed_by_anyone()
    {
        $resource = Resource::first();

        foreach([$this->adminAuthHeader,[]] as $header) {

            $response = $this->json('get',route('resources.show',$resource->id),[],$header);

            $response
                ->assertJsonStructure([
                    'title',
                    'id',
                    'type'
                ])
                ->assertStatus(Response::HTTP_OK);
        }
    }

    public function test_a_pdf_file_resource_is_downloadable()
    {
        $files = Resource::with('resourceable')->isFile()->take(1)->get();

        foreach($files as $resource) {
            $res = $this->json('post',route('resources.download',$resource->id),[],[]);

            $fileName = Str::remove(config('filesystems.file_dir'),$resource->file->path);
            $fileName = Str::after($fileName,"/");

            $res->assertDownload( $fileName );
        }
    }

    public function test_file_resource_can_not_be_created_without_pdf_file()
    {
        $fileExtensions = [
            'hello.c',
            'hello.cpp',
            'hello.java',
            'hello.sh',
            'hello.mp4',
            'hello.mp3',
            'hello.png',
            'hello.jpg',
            'hello.jpeg',
            'hello.svg',
            'hello.docx',
            'hello.xlsx',
            'hello.zip',
            'hello.gif',
            'hello.jar',
            'hello.dmg',
            'hello.exe',
            'hello.php',
            'hello.go',
        ];

        $testData = [
            'title' => $this->faker->realTextBetween(5, 250),
            'resource_type' => 'file',
        ];

        foreach($fileExtensions as $fe) {

            $testData['file'] = UploadedFile::fake()->create($fe);

            $response = $this->json('post',route('resources.store'),$testData,$this->adminAuthHeader);

            $response
                ->assertJson([
                    'errors' =>[
                        'file' => ['The file must be a file of type: pdf.']
                    ]
                ])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function test_file_upload_accepts_files_upto_five_megabytes()
    {
        $testData = [
            'title' => $this->faker->realText(mt_rand(10,40)),
            'resource_type' => 'file',
            'file' =>  UploadedFile::fake()->create('hello.pdf', 5123 ),
        ];

        $response = $this->json('post',route('resources.store'),$testData,$this->adminAuthHeader);

        $response
            ->assertJson([
                'errors' =>[
                    'file' => ['The file must not be greater than 5120 kilobytes.']
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $testData['file'] = UploadedFile::fake()->create('hello.pdf', 1234 );

        $response = $this->json('post',route('resources.store'),$testData,$this->adminAuthHeader);

        $response->assertStatus(Response::HTTP_CREATED);

    }

    public function test_a_non_pdf_file_resource_returns_422_when_requested_for_download()
    {
        $links = Resource::isLink()->get();
        $html  = Resource::isHtmlSnippet()->get();

        $links->merge($html)
            ->each(function ($resource) {
                $res = $this->json('post',route('resources.download',$resource->id),[],[]);

                $res
                    ->assertJson([
                        'success' => false,
                        'message' => 'resource is not a downloadable type.'
                    ])
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            });
    }

    public function test_a_resource_can_be_fetched_for_edit()
    {
        $fileResource = Resource::isFile()->first();

        $this->json('get',route('resources.edit',$fileResource->id),[],$this->adminAuthHeader)
            ->assertJson([
                'id' => $fileResource->id,
                'title' => $fileResource->title,
                'type' => $fileResource->type,
                'file' => [
                    'abs_url' => $fileResource->file->abs_url,
                    'path'    => $fileResource->file->path,
                ]
            ])
            ->assertStatus(Response::HTTP_OK);

        $linkResource = Resource::isLink()->first();

        $this->json('get',route('resources.edit',$linkResource->id),[],$this->adminAuthHeader)
            ->assertJson([
                'id' => $linkResource->id,
                'title' => $linkResource->title,
                'type' => $linkResource->type,
                'link' => [
                    'link' => $linkResource->resourceable->link,
                    'opens_in_new_tab'    => $linkResource->resourceable->opens_in_new_tab,
                ]
            ])
            ->assertStatus(Response::HTTP_OK);

        $htmlResource = Resource::isHtmlSnippet()->first();

        $this->json('get',route('resources.edit',$htmlResource->id),[],$this->adminAuthHeader)
            ->assertJson([
                'id'    => $htmlResource->id,
                'title' => $htmlResource->title,
                'type'  => $htmlResource->type,
                'html_snippet' => [
                    'markup'      => $htmlResource->resourceable->markup,
                    'description' => $htmlResource->resourceable->description,
                ]
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_user_should_be_able_to_get_a_paginated_list_of_all_resources()
    {
        $this->json('get',route('resources.index'),[],[])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10,'data'); //default pagination
    }
}
