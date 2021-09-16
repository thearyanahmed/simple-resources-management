<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class Resource extends Model
{
    use HasFactory;

    const RESOURCE_LINK         = 'link';
    const RESOURCE_FILE         = 'file';
    const RESOURCE_HTML_SNIPPET = 'html_snippet';

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'title', 'resourceable_type', 'resourceable_id'
    ];

    protected $appends = [
        'resource_type'
    ];

    protected $hidden = [
        'resourceable_type', 'resourceable_id'
    ];

    public function resourceable()
    {
        return $this->morphTo();
    }

    public function getResourceTypeAttribute()
    {
        return ([
            Link::class => Resource::RESOURCE_LINK,
//            Link::class => Resource::RESOURCE_PDF,
//            Link::class => Resource::RESOURCE_HTML_SNIPPET,
        ])[$this->resourceable_type];
    }
}
