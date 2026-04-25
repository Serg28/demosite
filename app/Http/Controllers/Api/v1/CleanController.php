<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\MaintenanceCategories;
use App\Jobs\RegeneratePagesCache;
use App\Jobs\RegenerateProductsCache;
use App\Jobs\RegenerateProductsPreviews;
use App\Jobs\RegenerateSettingsCache;
use App\Jobs\ReindexAllProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class CleanController extends ApiController
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    public function cleanCache(): JsonResponse
    {
        MaintenanceCategories::dispatch();
        ReindexAllProducts::dispatch();
        RegenerateProductsPreviews::dispatch();
        RegenerateSettingsCache::dispatch();
        RegeneratePagesCache::dispatch();
        return response()->json(['message' => 'CleanController started'], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function rebuild(): JsonResponse
    {
        MaintenanceCategories::dispatch();
        //ReindexAllProducts::dispatch();
        //RegenerateProductsPreviews::dispatch();
        //RegenerateSettingsCache::dispatch();
        //RegeneratePagesCache::dispatch();
        return response()->json(['message' => 'CleanController started: rebuild categories'], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
