<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection as RC;

class ResourceCollection extends RC
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
