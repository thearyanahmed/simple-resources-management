<?php

namespace App\Http\Resources;

use App\Models\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = $this->resource['resource']->toArray();

        $response[ Resource::RESOURCE_LINK ]         = [];
        $response[ Resource::RESOURCE_PDF ]          = [];
        $response[ Resource::RESOURCE_HTML_SNIPPET ] = [];

        $response[ $this->resource['resource_type'] ] = $this->resource[ $this->resource['resource_type'] ];

        return $response;
    }
}
