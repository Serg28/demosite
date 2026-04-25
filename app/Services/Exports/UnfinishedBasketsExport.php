<?php

namespace App\Services\Exports;

use App\Models\UnfinishedBasket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UnfinishedBasketsExport implements FromQuery, WithTitle, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return UnfinishedBasket::query()
            ->when($this->request->input('date_from'), function ($query) {
                return $query->where('created_at', '>=', $this->request->input('date_from'));
            })
            ->when($this->request->input('date_to'), function ($query) {
                $dateTo = Carbon::parse($this->request->input('date_to'))
                    ->addHour(23)
                    ->addMinute(59)
                    ->addSecond(59)
                    ->toDateTimeString();

                return $query->where('created_at', '<=', $dateTo);
            })
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'id',
            'Хеш корзины',
            'Товары',
            'Дата',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->hash_basket,
            $this->getProducts($row),
            $row->created_at->format('d-m-Y H:i'),
        ];
    }

    public function getProducts($row)
    {
        $str = '';

        if ($row->products->isNotEmpty()) {
            $row->products->each(function ($basket) use (&$str): void {
                $str .= 'Название товара: '.$basket->product->t('title').', '
                    .'К-во: '.$basket->count.', '
                    .'Цена'.$basket->price.'; ';
            });
        }

        return $str ?: '-';
    }

    public function title(): string
    {
        return 'unfinished_basket';
    }
}
