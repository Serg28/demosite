<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ModelBrandRequest;
use App\Http\Resources\ModelBrandResource;
use App\Jobs\MaintenanceCatalog;
use App\Models\ModelBrand;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder as RB;

class CatalogController extends ApiController
{
    //Обслуживание каталога товаров
    public function rebuild()
    {
        try {
            MaintenanceCatalog::dispatch();
            return RB::asSuccess()->withMessage('CatalogController: начат фоновый процесс обслуживания каталога товаров')->build();
        } catch (\Exception $exception) {
            return RB::asError()->withMessage('CatalogController: ' . $exception->getMessage())->build();
        }
    }
}
