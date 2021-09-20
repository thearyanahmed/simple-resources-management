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
        collect([
             Link::class, HtmlSnippet::class,
        ])
            ->each(function ($model){
                $model::factory()->count(100)->create()->each(function ($row) use ($model) {
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
        for($i = 0; $i < 10; $i++) {
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
