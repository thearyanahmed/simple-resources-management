<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleResourceCreatedResponse extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'success' => true,
            'message'  => 'resource created successfully.',
            'resource' => new SingleResourceResponse($this->resource)
        ];
    }
}
