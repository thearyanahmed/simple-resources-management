<?php

namespace App\Factories;

use App\Models\File;
use App\Models\Link;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;

class CreateResourceFactory
{
    protected string $resourceType;
    protected Model $relatedResourceModel;

    public function __construct(array $data)
    {
        $this->resourceType = $data['resource_type'];

        $relatedModel = self::getResourceableType($data['resource_type']);
        $this->relatedResourceModel = new $relatedModel;
    }

    private static function getResourceableType(string $resourceType) : string
    {
        return ([
            Resource::RESOURCE_LINK         => Link::class,
            Resource::RESOURCE_FILE          => File::class,
            Resource::RESOURCE_HTML_SNIPPET => Link::class,
        ])[$resourceType];
    }


}
