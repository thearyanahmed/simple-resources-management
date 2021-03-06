<?php

namespace App\Models;

use App\Traits\DeletesStorageFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, DeletesStorageFile;

    protected $fillable = [
        'abs_url','path','disk',
    ];

    protected $hidden = [
        'id', 'created_at','updated_at','disk',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function($file) {
            self::deleteFile($file->disk,$file->path,'after delete');
        });
    }

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }

}
