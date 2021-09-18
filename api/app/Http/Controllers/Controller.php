<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const DEFAULT_ITEMS_PER_PAGE = 10;
    const MAX_ITEMS_PER_PAGE = 10;

    protected function perPage()
    {
        $perPage = request()->get('per_page', self::DEFAULT_ITEMS_PER_PAGE);

        if ($perPage < 0 || $perPage > self::MAX_ITEMS_PER_PAGE) {
            return self::DEFAULT_ITEMS_PER_PAGE;
        }

        return $perPage;
    }

    protected function orderBy(string $column = 'created_at')
    {
        return request('order_by',$column);
    }

    protected function orderDirection(string $dir = 'desc')
    {
        return request('order_direction',$dir);
    }
}
