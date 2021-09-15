<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link', 'opens_in_new_tab'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
}
