<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'abs_path'
    ];

    protected $hidden = [
        'id', 'created_at','updated_at'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }

    public static function store()
    {

        dd('implement file store');
    }
}
