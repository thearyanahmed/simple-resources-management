<?php

namespace App\Mutators;

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

    protected function uploadFile(string $disk, string $dir, $contents): string
    {
        return Storage::disk($disk)->put($dir , $contents);
    }

    protected function fileUrl(string $uploadedFilePath): string
    {
        return url('/') . Storage::url($uploadedFilePath);
    }
}
