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

    protected $hidden = [
        'id', 'created_at','updated_at'
    ];

    protected $casts = [
        'opens_in_new_tab' => 'boolean'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
}
