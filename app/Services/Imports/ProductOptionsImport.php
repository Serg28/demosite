<?php

namespace App\Services\Imports;

use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductOptionsImport implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts
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
        if (! $row['id']) {
            ProductCharacteristicOption::withoutEvents(function () use ($row) {
                return ProductCharacteristicOption::create($this->prepareData($row));
            });
        }
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        return [
            'product_id' => $data['product_id'],
            'characteristic_id' => $this->getCharacteristicId($data),
            'characteristic_option_id' => $this->getCharacteristicOptionId($data),
        ];
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function getCharacteristicOptionId(array $data): int
    {
        $characteristicId = $this->getCharacteristicId($data);

        $option = CharacteristicOption::whereJsonContains('title->'.$this->thisLocale, $data['characteristic_option'])
            ->where('characteristic_id', $characteristicId)
            ->first();

        if (! $option) {
            $option = CharacteristicOption::create([
                'characteristic_id' => $characteristicId,
                'title' => $this->decodeJsonField($data['characteristic_option']),
                'is_active' => 1,
                'slug' => Str::slug($data['characteristic_option']),
            ]);
        }

        return $option->id;
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function getCharacteristicId(array $data): int
    {
        $characteristic = Characteristic::whereJsonContains('title->'.$this->thisLocale, $data['characteristic'])->first();

        if (! $characteristic) {
            $characteristic = Characteristic::create([
                'title' => $this->decodeJsonField($data['characteristic']),
                'is_active' => 1,
                'slug' => Str::slug($data['characteristic']),
            ]);

            $product = Product::find($data['product_id']);
            $product->category->characteristics()->attach($characteristic->id);
        }

        return $characteristic->id;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    private function decodeJsonField(string $value): mixed
    {
        return json_encode([$this->thisLocale => $value, 'ru' => '', 'en' => '']);
    }
}
