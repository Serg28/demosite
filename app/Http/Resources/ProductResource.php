<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        //return parent::toArray($request);
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

        $data = parent::toArray($request);

        //Поля с предварительной обработкой
        $fieldsToDecode = ['title', 'short_description', 'description', 'url'];
        foreach ($fieldsToDecode as $field) {
            $data[$field] = isset($data[$field]) ? json_decode($data[$field]) : null;
        }

        return $data + [
            'picture' => geturl($data['picture'] ?? null),
            'other_pictures' => $this->getOtherPictures($this),
            'status' => 'available',
            'quantity' => $this->getQuantity(),
            'filters' => $this->getFilters($this),
        ];
    }

    private function getOtherPictures($item)
    {
        return collect(json_decode($item->other_pictures))->map(function ($picture) {
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
