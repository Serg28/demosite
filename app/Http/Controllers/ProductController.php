<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BreadcrumbsService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product, BreadcrumbsService $breadcrumbs): View
    {
        abort_if(! $product->is_active, 404);

        $product->load('characteristics.characteristic');

        return view('product.show', [
            'product'     => $product,
            'breadcrumbs' => $breadcrumbs->forProduct($product),
        ]);
    }
}
