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

        unset($response['resourceable_type']); // unset ?

//        $emptyObject = (object) []; // send empty objects?

//        $response[ Resource::RESOURCE_LINK ]         = $emptyObject; // using this unless the response would have array for
//        $response[ Resource::RESOURCE_PDF ]          = $emptyObject;
//        $response[ Resource::RESOURCE_HTML_SNIPPET ] = $emptyObject;

        $response[ $this->resource['resource_type'] ] = $this->resource[ $this->resource['resource_type'] ];

        return $response;
    }
}
