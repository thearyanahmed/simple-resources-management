<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'abs_url','path','disk',
    ];

    protected $hidden = [
        'id', 'created_at','updated_at','disk',
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }

}
