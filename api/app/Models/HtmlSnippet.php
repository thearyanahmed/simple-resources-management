<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HtmlSnippet extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 'markup'
    ];

    protected $hidden = [
        'id', 'created_at','updated_at'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
}
