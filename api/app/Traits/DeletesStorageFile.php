<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Exception;

trait DeletesStorageFile
{
    private static function deleteFile(string $disk, string $path,string $action)
    {
        try {
            Storage::disk($disk)->delete($path);
        } catch (Exception $e) {

            logger()->error('error_deleting_file_from_hosting_server',[
                'during' => $action,
                'exception_msg' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
            ]);
        }
    }
}
