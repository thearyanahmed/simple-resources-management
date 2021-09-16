<?php

namespace App\Factories;

use App\Models\File;
use App\Models\Link;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateResourceFactory
{
    protected string $relatedResourceType;

    protected Model $relatedResourceModel;
    protected array $relatedResourceData;

    protected array $resourceData;

    /**
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->relatedResourceType = $data['resource_type'];

        $this->relatedResourceData =Arr::only($data,$this->requiredRelatedResources());
        $this->relatedResourceModel = new (self::getResourceableType($this->relatedResourceType));

        $this->resourceData = [
            'title'             => $data['title'],
            'resourceable_type' => $this->relatedResourceModel::class,
            'resourceable_id'   => null,
        ];
    }

    private static function getResourceableType(string $relatedResourceType) : string
    {
        return ([
            Resource::RESOURCE_LINK         => Link::class,
            Resource::RESOURCE_FILE         => File::class,
            Resource::RESOURCE_HTML_SNIPPET => Link::class,
        ])[$relatedResourceType];
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    public function create() : Resource
    {
        DB::beginTransaction();

        try {
            // create the related resource type
            $relatedResource = $this->createRelatedResource();

            // assign its model id for relation
            $this->resourceData['resourceable_id'] = $relatedResource->id;

            // create resource
            $resource = Resource::create($this->resourceData);

            // commit the transaction
            DB::commit();

            // assign related resource with specific key
            $resource->{$this->relatedResourceType} = $relatedResource;

            //TODO [improvement?] use DTO ?
            return $resource;

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @return string[]
     * @throws \Exception
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

        throw new \Exception('unsupported resource type');
    }

    /**
     * @return Model
     * @throws \Exception
     */
    private function createRelatedResource() : Model
    {
        if($this->relatedResourceModel::class === Link::class) {
            return $this->createLink();
        }

        if($this->relatedResourceModel::class === File::class) {
            return $this->createFile();
        }

        // todo change link:class to html markup
        if($this->relatedResourceModel::class === Link::class) {
            return $this->createHtmLSnippet();
        }

        throw new \Exception('unsupported resource');
    }

    private function createLink() : Link
    {
        return Link::create($this->relatedResourceData);
    }

    private function createFile() : File
    {
        // todo handle file upload
        return File::make();
    }

    // TODO change return type
    private function createHtmLSnippet() : File
    {
        // todo change creation
        return File::make();
    }

}
