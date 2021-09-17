<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property integer $id
 * @property string $resourceable_type
 * @property string $type
 * @method static create(array $resourceData)
 */
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
        'type'
    ];

    protected $hidden = [
        'resourceable_type', 'resourceable_id'
    ];

    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeAttribute(): string
    {
        return ([
            Link::class        => Resource::RESOURCE_LINK,
            File::class        => Resource::RESOURCE_FILE,
            HtmlSnippet::class => Resource::RESOURCE_HTML_SNIPPET,
        ])[$this->resourceable_type];
    }
}
