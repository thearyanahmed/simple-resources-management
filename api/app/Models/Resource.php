<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property integer $id
 * @property string $resourceable_type
 * @property string $title
 * @property string $type
 * @property MorphTo resourceable
 *
 * @method static create(array $resourceData)
 * @method filter(array $filters = [])
 * @method static isHtmlSnippet()
 * @method static isLink()
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

    public function scopeIsLink($query)
    {
        return $query->where('resourceable_type',Link::class);
    }

    public function scopeIsFile($query)
    {
        return $query->where('resourceable_type',File::class);
    }

    public function scopeIsHtmlSnippet($query)
    {
        return $query->where('resourceable_type',HtmlSnippet::class);
    }

    public function isTypeOf(string $type) : bool
    {
        return $this->type === $type;
    }

    public function downloadFile(string|null $title = null, array $headers = [])
    {
        $headers['Content-Type'] = 'application/pdf';

        return Storage::download($this->file->path, $title, $headers);
    }

    /**
     * @throws Exception
     */
    public function file(): MorphTo
    {
        if($this->type  !== Resource::RESOURCE_FILE) {
            throw new Exception('attempt to access file while resource is not a file type.');
        }

        return $this->resourceable();
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['title'] ?? null, function ($query, $title) {
                $query->where('title', 'like', '%' . $title . '%');
            })
            ->when($filters['resource_type'] ?? null,function ($query, $resourceType) {
                if($resourceType === 'any') {
                    return $query;
                }

                $typeClass = self::getResourceableType($resourceType);

                if($typeClass === null) {
                    return $query;
                }

                return $query->where('resourceable_type',$typeClass);
            });
    }

    public static function getResourceableType(string $relatedResourceType) : string|null
    {
        return ([
            Resource::RESOURCE_LINK         => Link::class,
            Resource::RESOURCE_FILE         => File::class,
            Resource::RESOURCE_HTML_SNIPPET => HtmlSnippet::class,
        ])[$relatedResourceType] ?? null;
    }
}
