<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeleteResourceResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'message' => 'resource deleted successfully.'
        ];
    }
}
