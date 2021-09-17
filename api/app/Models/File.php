<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'abs_url','path','disk',
    ];

    protected $hidden = [
        'id', 'created_at','updated_at','disk',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($file) {
            try {
                Storage::disk($file->disk)->delete($file->path);
            } catch (Exception $e) {

                logger()->error('error_deleting_file_from_hosting_server',[
                    'exception_msg' => $e->getMessage(),
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine(),
                    'model_id'       => $file->id,
                    'file_path'      => $file->path
                ]);
            }
        });
    }

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }

}
