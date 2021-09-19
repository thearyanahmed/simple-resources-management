<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleResourceUpdateResponse extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'success'  => true,
            'message'  => 'resource has been updated successfully.',
            'resource' => new SingleResourceResponse($this->resource)
        ];
    }
}
