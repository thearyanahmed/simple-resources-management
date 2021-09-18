<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\HtmlSnippet;
use App\Models\Link;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        collect([
            Link::class, HtmlSnippet::class, File::class,
        ])
            ->each(function ($model){
                $model::factory()->count(35)->create()->each(function ($link) use ($model) {
                    Resource::factory()->count(1)->create([
                        'resourceable_type' => $model,
                        'resourceable_id'   => $link->id
                    ]);
                });
            });
    }
}
