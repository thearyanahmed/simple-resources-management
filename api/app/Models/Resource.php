<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class Resource extends Model
{
    use HasFactory;

    const RESOURCE_LINK         = 'link';
    const RESOURCE_PDF          = 'pdf';
    const RESOURCE_HTML_SNIPPET = 'html_snippet';

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'title', 'resourceable_type', 'resourceable_id'
    ];

    public function resourceable()
    {
        return $this->morphTo();
    }

    /**
     * @param array $data
     * @return Resource
     * Use DTO? overkill?
     * Set it in a service layer? no?
     * @throws Throwable
     */
    public static function createResource(array $data): Resource
    {
        // db transaction
        DB::beginTransaction();

        try {

            $relatedResourceModel = self::getResourceableType($data['resource_type']);

            $resource = [
                'title'             => $data['title'],
                'resourceable_type' => $relatedResourceModel,
            ];

            $relatedResourceData = Arr::except($data,['title','resource_type']);
            $relatedResource = $relatedResourceModel::create($relatedResourceData);

            $resource['resourceable_id'] = $relatedResource->id;

            $resource = Resource::create($resource);

            DB::commit();

            $resource->{$data['resource_type']} = $relatedResource;
            // use DTO ?
            return $resource;

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    // todo change model mapping
    private static function getResourceableType(string $resourceType) : string
    {
        return ([
            Resource::RESOURCE_LINK         => Link::class,
            Resource::RESOURCE_PDF          => Link::class,
            Resource::RESOURCE_HTML_SNIPPET => Link::class,
        ])[$resourceType];
    }
}
