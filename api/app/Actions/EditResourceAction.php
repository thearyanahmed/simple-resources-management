<?php

namespace App\Actions;

use Throwable;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\{File,HtmlSnippet,Link,Resource};

class EditResourceAction
{
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
        $this->resource = $resource;
        $this->relatedResource = $relatedResource;

        $this->relatedResourceType = $formData['resource_type'];
        $this->relatedResourceData  = Arr::only($formData,$this->requiredRelatedResources());

        $this->resourceData = ['title' => $formData['title']];
    }

    /**
     * @return string[]
     * @throws Exception
     */
    private function requiredRelatedResources() : array
    {
        if($this->relatedResourceType === Resource::RESOURCE_LINK) {
            return ['link','opens_in_new_tab'];
        }

        if($this->relatedResourceType === Resource::RESOURCE_HTML_SNIPPET) {
            return ['markup','description'];
        }

        if($this->relatedResourceType === Resource::RESOURCE_FILE) {
            return ['file'];
        }

        throw new Exception('unsupported resource type');
    }

    public function edit(Resource $resource) : Resource
    {
        DB::beginTransaction();

        try {
            $this->updateRelatedResource();

            $resource->update($this->resourceData);

            // commit the transaction
            DB::commit();

            return $resource;

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function updateRelatedResource() : Model
    {
        if($this->resource->type === Resource::RESOURCE_LINK) {
            return $this->updateLink();
        }

        if($this->resource->type === Resource::RESOURCE_FILE) {
            return $this->updateFile();
        }

        if($this->resource->type === Resource::RESOURCE_HTML_SNIPPET) {
            return $this->updateHtmLSnippet();
        }

        throw new Exception('unsupported resource');
    }

    private function updateLink()
    {
        return Link::make();
    }

    private function updateFile()
    {
        return File::make();
    }

    private function updateHtmLSnippet()
    {
        return HtmlSnippet::make();
    }
}
