<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dir = config('filesystems.file_dir');

        $file = UploadedFile::fake()->create(uniqid() . '.pdf' );

        $disk = config('filesystems.default');

        $uploadedFilePath = Storage::disk($disk)->put($dir, $file);

        $url = Storage::url($uploadedFilePath);

        return [
            'disk'    => $disk,
            'path'    => $uploadedFilePath,
            'abs_url' => $url
        ];
    }
}
