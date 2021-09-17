<?php

namespace App\Factories;

use App\Models\File;
use App\Models\HtmlSnippet;
use App\Models\Link;
use App\Models\Resource;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CreateResourceFactory
{
    protected string $relatedResourceType;

    protected Model $relatedResourceModel;
    protected array $relatedResourceData;

    protected array $resourceData;

    /**
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->relatedResourceType = $data['resource_type'];

        $this->relatedResourceData  = Arr::only($data,$this->requiredRelatedResources());
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
            Resource::RESOURCE_HTML_SNIPPET => HtmlSnippet::class,
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

    /**
     * @return Model
     * @throws Exception
     */
    private function createRelatedResource() : Model
    {
        if($this->relatedResourceModel::class === Link::class) {
            return $this->createLink();
        }

        if($this->relatedResourceModel::class === File::class) {
            return $this->createFile();
        }

        if($this->relatedResourceModel::class === HtmlSnippet::class) {
            return $this->createHtmLSnippet();
        }

        throw new Exception('unsupported resource');
    }

    private function createLink() : Link
    {
        return Link::create($this->relatedResourceData);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function createFile() : File
    {
        $disk = config('filesystems.default');

        $dir = 'resources/pdfs';

        $uploadedFilePath = Storage::disk($disk)->put($dir ,$this->relatedResourceData['file']);

        $url = Storage::url($uploadedFilePath);

        return File::create([
            'disk'    => $disk,
            'path'    => $uploadedFilePath,
            'abs_url' => $url
        ]);
    }

    private function createHtmLSnippet() : HtmlSnippet
    {
        return HtmlSnippet::create($this->relatedResourceData);
    }

}
