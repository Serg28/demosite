<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ApiExamplesController extends Controller
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    public function news(): JsonResponse
    {
        $data = News::active()->limit(5)->get();

        $data = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => json_decode($item->title),
                'slug' => $item->slug,
                'short_description' => json_decode($item->short_description),
                'description' => json_decode($item->description),
                'is_active' => $item->is_active,
                'picture' => $item->picture ? geturl($item->picture) : null,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function categories(): JsonResponse
    {
        $data = Category::with('children')->defaultOrder()->get()->toTree();

        $result = $this->convertCategories($data);

        return response()->json($result, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function convertCategories($data)
    {
        if ($data->count() == 0) {
            return false;
        }

        return $data->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => json_decode($item->title),
                'slug' => $item->slug,
                'seo' => $this->getSeoCategory($item),
                'picture' => $item->picture ? geturl($item->picture) : null,
                'ico' => $item->ico ? geturl($item->ico) : null,
                'is_active' => $item->is_active,
                'children' => $this->convertCategories($item->children),
            ];
        });
    }

    private function getSeoCategory($item)
    {
        return [
            'title' => json_decode($item->seo->seo_title),
            'description' => json_decode($item->seo->seo_description),
            'text' => json_decode($item->seo->seo_text),
            'keywords' => json_decode($item->seo->seo_keywords),
        ];
    }

    public function products()
    {
        $data = Product::active()->limit(15)->get();

        $data = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'title' => json_decode($item->title),
                'slug' => $item->slug,
                'code' => $item->code,
                'price' => $item->price,
                'price_old' => $item->price_old,
                'short_description' => json_decode($item->short_description),
                'description' => json_decode($item->description),
                'is_active' => $item->is_active,
                'picture' => geturl($item->picture),
                'other_pictures' => $this->getOtherPictures($item),
                'link_to_youtube' => $item->link_to_youtube,
                'status' => 'available',
                'quantity' => 10,
                'filters' => $this->getFilters($item),
                'created_at' => $item->created_at,
            ];
        });

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function getOtherPictures($item)
    {
        $pictures = collect(json_decode($item->other_pictures));

        return $pictures->map(function ($picture) {
            return getUrl($picture);
        });
    }

    private function getFilters($item)
    {
        return $item->characteristics->map(function ($item) {
            return [
                'characteristic' => [
                    'id' => $item->characteristic->id,
                    'name' => json_decode($item->characteristic->title),
                ],
                'value' => [
                    'id' => $item->characteristicOption->id,
                    'name' => json_decode($item->characteristicOption->title),
                ],
            ];
        });
    }
}
