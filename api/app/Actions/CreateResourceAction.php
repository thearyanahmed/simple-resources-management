<?php

namespace App\Actions;

use App\Traits\Form\RequiredResourceFieldsInFormRequest;
use Throwable;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB,Storage};
use App\Models\{File,HtmlSnippet,Link,Resource};

class CreateResourceAction
{
    use RequiredResourceFieldsInFormRequest;

    protected string $relatedResourceType;

    protected Resource $resource;
    protected Model $relatedResourceModel;
    protected array $relatedResourceData;

    protected array $resourceData;

    /**
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->relatedResourceType = $data['resource_type'];

        $this->relatedResourceData  = Arr::only($data,$this->requiredRelatedResources($this->relatedResourceType));
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

            return $resource;

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @return Model
     * @throws Exception|Throwable
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

    private function createHtmLSnippet() : HtmlSnippet
    {
        return HtmlSnippet::create($this->relatedResourceData);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function createFile() : File
    {
        $disk = config('filesystems.default');

        $dir = config('filesystems.file_dir');

        $uploadedFilePath = $this->uploadFile($disk,$dir);

        $url = Storage::url($uploadedFilePath);

        return File::create([
            'disk'    => $disk,
            'path'    => $uploadedFilePath,
            'abs_url' => $url
        ]);
    }

    private function uploadFile(string $disk, string $dir)
    {
        return Storage::disk($disk)->put($dir ,$this->relatedResourceData['file']);
    }


}
