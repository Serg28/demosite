<?php

namespace App\Services\Exports;

use App\Models\ProductCharacteristicOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductOptions implements FromQuery, WithTitle, WithHeadings, ShouldAutoSize, WithMapping
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query(): Builder
    {
        return ProductCharacteristicOption::query()->orderBy('product_id');
    }

    /**
     * @return array<string, string>
     */
    public function headings(): array
    {
        return [
            'id',
            'product_id',
            'characteristic',
            'characteristic_option',
            'sku',
            'title',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->product_id,
            $row->characteristic->t('title'),
            $row->characteristicOption->t('title'),
            $row->product->code,
            $row->product->t('title'),
        ];
    }

    public function title(): string
    {
        return 'options';
    }
}
