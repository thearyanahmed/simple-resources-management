<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\HtmlSnippet;
use App\Models\Link;
use App\Models\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    const DEMO_PDF = 'resources/pdfs/sample-pdf-file.pdf';
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->populate();

        $this->populateInRandomOrder();
    }

    private function populateInRandomOrder()
    {
        $disk = 'local';

        $url = url('/') . Storage::url(self::DEMO_PDF);

        $models = [
            Link::class, HtmlSnippet::class, File::class,
        ];

        for($i =0 ; $i < 15; $i++) {

            $selectedModel = $models[mt_rand(0,2)];

            if($selectedModel === Link::class || $selectedModel === HtmlSnippet::class) {
                $records = $selectedModel::factory()->count(mt_rand(1,3))->create();
            } else {
                $files = [];
                // bypassing multiple dummy file upload
                for($i = 0; $i < mt_rand(1,3); $i++) {
                    $file = File::create([
                        'disk' => $disk,
                        'path' => self::DEMO_PDF,
                        'abs_url' => $url
                    ]);

                    array_push($files,$file);
                }

                $records = collect($files);
            }

            $records->each(function ($row) use ($selectedModel) {
                Resource::factory()->count(1)->create([
                    'resourceable_type' => $selectedModel,
                    'resourceable_id'   => $row->id
                ]);
            });
        }
    }

    private function populate()
    {
        collect([
            Link::class, HtmlSnippet::class,
        ])
            ->each(function ($model){
                $model::factory()->count(15)->create()->each(function ($row) use ($model) {
                    Resource::factory()->count(1)->create([
                        'resourceable_type' => $model,
                        'resourceable_id'   => $row->id
                    ]);
                });
            });

        $disk = 'local';

        $url = url('/') . Storage::url(self::DEMO_PDF);

        $files = [];

        // bypassing multiple dummy file upload
        for($i = 0; $i < 15; $i++) {
            $file = File::create([
                'disk' => $disk,
                'path' => self::DEMO_PDF,
                'abs_url' => $url
            ]);

            array_push($files,$file);
        }

        collect($files)->each(function ($row) {
            Resource::factory()->count(1)->create([
                'resourceable_type' => File::class,
                'resourceable_id'   => $row->id
            ]);
        });
    }
}
