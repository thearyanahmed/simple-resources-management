<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Exception;

trait DeletesStorageFile
{
    public static function bootDeletesStorageFile()
    {
        static::updated(function ($file) {
            if($file->isDirty('path')) {
                $this->deleteFile($file,'after update');
            }
        });

        static::deleted(function ($file) {
            self::deleteFile($file,'after delete');
        });
    }

    private static function deleteFile(File $file,string $action)
    {
        try {
            Storage::disk($file->disk)->delete($file->path);
        } catch (Exception $e) {

            logger()->error('error_deleting_file_from_hosting_server',[
                'during' => $action,
                'exception_msg' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'model_id'       => $file->id,
                'file_path'      => $file->path
            ]);
        }
    }
}
