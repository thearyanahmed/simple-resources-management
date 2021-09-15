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
     * @return array
     * Use DTO? overkill?
     * @throws Throwable
     */
    public static function createResource(array $data): array
    {
        // db transaction
        DB::beginTransaction();

        try {
            $resource = [
                'title'             => $data['title'],
                'resourceable_type' => self::getResourceableType($data['resource_type'])
            ];

            $relatedResourceData = Arr::except($data,['title','resource_type']);
            $relatedResource = $resource['resourceable_type']::create($relatedResourceData);

            $resource['resourceable_id'] = $relatedResource->id;

            $resource = Resource::create($resource);

            DB::commit();

            // need DTO ?
            return [
                'resource'             => $resource,
                $data['resource_type'] => $relatedResource
            ];

        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    // todo change model mapping
    private static function getResourceableType(string $resourceType) : string
    {
        return ([
            'link' => Link::class,
            'pdf' => Link::class,
            'html_snippet' => Link::class,
        ])[$resourceType];
    }

}
