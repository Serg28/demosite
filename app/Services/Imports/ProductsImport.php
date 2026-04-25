<?php

namespace App\Services\Imports;

use function App\Imports\count;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts
{
    private string $thisLocale;

    public function __construct()
    {
        $this->thisLocale = defaultLanguage();
    }

    /**
     * @param  array<string,mixed>  $row
     */
    public function model(array $row): void
    {
        Product::withoutEvents(function () use ($row) {
            return Product::updateOrCreate(
                [
                    'id' => $row['product_id'],
                ],
                $this->prepareData($row)
            );
        });
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        if ($data['product_id']) {
            $product = Product::find($data['product_id']);
            $titleProduct = json_decode($product->title);
            $shortDescriptionProduct = json_decode($product->short_description);
            $sdescriptionProduct = json_decode($product->description);
        }

        return [
            'code' => $data['sku'],
            'category_id' => $this->getCategoryId($data['category']),
            'slug' => $this->getSlug($data),
            'title' => json_encode([
                $this->thisLocale => $data['title'],
                'ru' => $titleProduct->ru ?? '',
                'en' => $titleProduct->en ?? '',
            ]),
            'short_description' => json_encode([
                $this->thisLocale => $data['short_description'],
                'ru' => $shortDescriptionProduct->ru ?? '',
                'en' => $shortDescriptionProduct->en ?? '',
            ]),
            'description' => json_encode([
                $this->thisLocale => $data['description'],
                'ru' => $sdescriptionProduct->ru ?? '',
                'en' => $sdescriptionProduct->en ?? '',
            ]),
            'price' => $data['price'] ?? 0,
            'price_old' => $data['price_old'] ?? 0,
            'picture' => $this->getPicture($data['picture']),
            'other_pictures' => $this->getPictures($data['other_pictures']),
            'is_active' => $data['is_active'],
            'quantity' => $data['quantity'] ?? 0,
            'product_status_id' => $data['status'] ?? 1,
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 10;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function getSlug(array $data): string
    {
        if (Product::where('slug', $data['slug'])->where('id', '!=', $data['product_id'])->exists()) {
            return $data['slug'].time();
        }

        return $data['slug'];
    }

    private function getCategoryId(string $categories): ?string
    {
        $categoriesArray = explode('/', $categories);

        if (is_array($categoriesArray)) {
            $categoriesArrayReverse = array_reverse($categoriesArray);

            if (count($categoriesArrayReverse) > 1) {
                $parentCategory = Category::whereJsonContains(
                    'title->'.$this->thisLocale,
                    $categoriesArrayReverse[1]
                )->first();
            } else {
                $parentCategory = Category::find(1);
            }

            if ($parentCategory) {
                $category = Category::whereJsonContains('title->'.$this->thisLocale, $categoriesArrayReverse[0])
                   ->where('parent_id', $parentCategory->id)
                   ->first();

                if ($category) {
                    return $category->id;
                }
            }
        }

        return null;
    }

    private function getPicture(string $picture): string
    {
        if ($picture) {
            $pictureName = pathinfo($picture)['basename'];

            try {
                $fullPathPicture = '/storage/editor/fotos/'.$pictureName;

                if (! file_exists(public_path($fullPathPicture))) {
                    @copy($picture, public_path($fullPathPicture));
                }

                return $fullPathPicture;
            } catch (Exception $e) {
                return '';
            }
        }
    }

    private function getPictures(string $pictures): mixed
    {
        if ($pictures) {
            $pictureCollections = explode(',', $pictures);
            $arrayPictures = [];

            foreach ($pictureCollections as $picture) {
                $arrayPictures[] = $this->getPicture($picture);
            }

            return json_encode($arrayPictures);
        }

        return '';
    }
}
