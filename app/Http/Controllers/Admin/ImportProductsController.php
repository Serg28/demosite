<?php

namespace App\Http\Controllers\Admin;

use App\Services\Imports\ProductsImport;
use Excel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class ImportProductsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '30000');
        set_time_limit(30000);

        Excel::import(new ProductsImport(), $request->file('file'));
        Artisan::call('cache:clear');
        Artisan::call('search:reindex');

        return response()->json(['status' => true]);
    }
}
