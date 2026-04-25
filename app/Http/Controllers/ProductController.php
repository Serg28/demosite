<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use App\Services\Product as ProductService;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function page(Product $product): View
    {
        $pageData = $this->productService->getPage($product);

        //return view('product.index', $pageData);
        return view($product->getTemplateProduct(), $pageData);
    }
}
