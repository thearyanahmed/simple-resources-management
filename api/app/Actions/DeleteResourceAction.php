<?php

namespace App\Actions;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;

class DeleteResourceAction
{
    private Resource $resource;

    /**
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function delete()
    {
        DB::beginTransaction();

        try {

            $this->resource->resourceable()->delete();
            $this->resource->delete();

            DB::commit();

            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
