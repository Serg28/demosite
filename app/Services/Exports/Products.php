<?php

namespace App\Services\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class Products implements FromQuery, WithTitle, WithHeadings, ShouldAutoSize, WithMapping
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query(): Builder
    {
        return Product::query()->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'product_id',
            'id_1c',
            'code',
            'price',
            'price_old',
            'is_active',
            'quantity',
            'status',
            'slug',
            'category',
            'title',
            'description',
            'short_description',
            'picture',
            'other_pictures',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->id_1c,
            $row->code,
            $row->price,
            $row->price_old,
            $row->is_active,
            $row->quantity,
            $row->product_status_id,
            $row->slug,
            $this->getCategories($row),
            $row->t('title'),
            $row->t('description'),
            $row->t('short_description'),
            $row->picture ? asset($row->picture) : '',
            $this->getPictures($row->other_pictures),
        ];
    }

    private function getCategories(Product $product): string
    {
        $collectionsCategories = $product->category ? $product->category->getAncestorsAndSelf() : collect();

        $categories = $collectionsCategories->map(function ($category) {
            return $category->t('title');
        })->toArray();

        if (is_array($categories)) {
            array_shift($categories);

            return implode('/', $categories);
        }

        return '';
    }

    private function getPictures(string $pictures): string
    {
        if ($pictures) {
            $arrayPictures = collect(json_decode($pictures));

            $picturesWithAsset = $arrayPictures->map(function ($picture) {
                return asset($picture);
            })->toArray();

            return implode(',', $picturesWithAsset);
        }

        return '';
    }

    public function title(): string
    {
        return 'products';
    }
}
