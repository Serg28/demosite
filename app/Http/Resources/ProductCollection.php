<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
        /*return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'title' =>  json_decode($this->title),
            'slug' => $this->slug,
            'code' => $this->code,
            'price' => $this->price,
            'price_old' => $this->price_old,
            'short_description' => json_decode($this->short_description),
            'description' => json_decode($this->description),
            'is_active' => $this->is_active,
            'picture' => geturl($this->picture),
            'other_pictures' => $this->getOtherPictures($this),
            'link_to_youtube' => $this->link_to_youtube,
            'status' => 'available',
            'quantity' => 10,
            'filters' => $this->getFilters($this),
            'created_at' => $this->created_at
        ];*/

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
