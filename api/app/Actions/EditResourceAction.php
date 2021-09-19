<?php

namespace App\Actions;

use App\Models\Resource;
use App\Mutators\ResourceMutator;
use App\Traits\DeletesStorageFile;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EditResourceAction extends ResourceMutator
{
    use DeletesStorageFile;

    protected string $relatedResourceType;

    protected Resource $resource;
    protected Model $relatedResource;

    protected array $relatedResourceData;

    protected array $resourceData;

    /**
     * @throws Exception
     */
    public function __construct(Resource $resource, Model $relatedResource, array $formData)
    {
        $this->resource        = $resource;
        $this->relatedResource = $relatedResource;

        $this->relatedResourceType = $formData['resource_type'];
        $this->relatedResourceData = Arr::only($formData,$this->requiredRelatedResources($this->relatedResourceType));
        logger()->error('rel',$this->relatedResourceData);
        $this->resourceData = ['title' => $formData['title']];
    }

    /**
     * @return Resource
     * @throws Throwable
     */
    public function edit() : Resource
    {
        DB::beginTransaction();

        try {
            $this->updateRelatedResource();

            $this->resource->update($this->resourceData);

            // commit the transaction
            DB::commit();

            $this->resource->fresh('resourceable');

            return $this->resource;

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function updateRelatedResource() : bool
    {
        if($this->resource->type === Resource::RESOURCE_LINK || $this->resource->type === Resource::RESOURCE_HTML_SNIPPET) {
            return $this->relatedResource->update($this->relatedResourceData);
        } elseif ($this->resource->type === Resource::RESOURCE_FILE) {

            if(! is_null(  $this->relatedResourceData['file'] ?? null)) {
                $this->updateFile();
                self::deleteFile($this->relatedResource,'after new file upload');
            }
            return true;
        }

        throw new Exception('unsupported resource');
    }

    private function updateFile() : void
    {
        $disk = config('filesystems.default');

        $dir = config('filesystems.file_dir');

        $uploadedFilePath = $this->uploadFile($disk,$dir, $this->relatedResourceData['file']);

        $url = $this->fileUrl($uploadedFilePath);

        $this->relatedResource->update([
            'disk' => $disk,
            'path' => $uploadedFilePath,
            'abs_url' => $url
        ]);
    }
}
