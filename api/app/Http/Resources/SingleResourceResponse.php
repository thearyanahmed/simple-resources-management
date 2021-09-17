<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleResourceResponse extends JsonResource
{
    public function toArray($request) : array
    {
        $response = $this->resource->toArray();

        $response[$this->resource->type] = $this->resource->resourceable;

        unset($response['resourceable']);

        return $response;
    }
}
