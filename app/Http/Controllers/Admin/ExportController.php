<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Exports\ProductOptions;
use App\Services\Exports\Products;
use App\Services\Exports\UnfinishedBasketsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private string $time;

    public function __construct()
    {
        $this->time = date('Y-m-d_H-i-s');
    }

    public function unfinishedBaskets(Request $request): BinaryFileResponse
    {
        return Excel::download(
            new UnfinishedBasketsExport($request),
            'unfinished_basket_'.$this->time.'.xlsx'
        );
    }

    public function products(Request $request): BinaryFileResponse
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '30000');
        set_time_limit(30000);

        return Excel::download(
            new Products($request),
            'products_'.$this->time.'.xlsx'
        );
    }

    public function productOptions(Request $request): BinaryFileResponse
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '30000');
        set_time_limit(30000);

        return Excel::download(
            new ProductOptions($request),
            'options_'.$this->time.'.xlsx'
        );
    }
}
