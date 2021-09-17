<?php

namespace App\Mutators;

use App\Models\File;
use App\Models\HtmlSnippet;
use App\Models\Link;
use App\Models\Resource;
use Exception;
use Illuminate\Support\Facades\Storage;

class ResourceMutator
{
    protected function requiredRelatedResources(string $relatedResourceType) : array
    {
        if($relatedResourceType === Resource::RESOURCE_LINK) {
            return ['link','opens_in_new_tab'];
        }

        if($relatedResourceType === Resource::RESOURCE_HTML_SNIPPET) {
            return ['markup','description'];
        }

        if($relatedResourceType === Resource::RESOURCE_FILE) {
            return ['file'];
        }

        throw new Exception('unsupported resource type');
    }

    protected function uploadFile(string $disk, string $dir, $contents)
    {
        return Storage::disk($disk)->put($dir , $contents);
    }

    protected function fileUrl(string $uploadedFilePath)
    {
        return Storage::url($uploadedFilePath);
    }

    protected function getResourceableType(string $relatedResourceType) : string
    {
        return ([
            Resource::RESOURCE_LINK         => Link::class,
            Resource::RESOURCE_FILE         => File::class,
            Resource::RESOURCE_HTML_SNIPPET => HtmlSnippet::class,
        ])[$relatedResourceType];
    }
}
