<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

trait Paginatable
{
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $paginator = parent::paginate($perPage, $columns, $pageName, $page);

        if ($page && $paginator->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        return $paginator;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($value === '1' && Request::has('page') && Request::get('page') === '1') {
            return Redirect::to(Request::url(), 301);
        }

        return parent::resolveRouteBinding($value, $field);
    }

    protected function isFirstPageWithPagination()
    {
        return request()->filled('page') && request()->query('page') == 1;
    }

}

