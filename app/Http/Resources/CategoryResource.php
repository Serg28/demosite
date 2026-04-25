<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        $data = parent::toArray($request);

        //Поля с предварительной обработкой
        $fieldsToDecode = ['title', 'synonym', 'synonym_plural', 'url'];
        foreach ($fieldsToDecode as $field) {
            $data[$field] = isset($data[$field]) ? json_decode($data[$field]) : null;
        }

        return $data;

        /*
        return [
            'id' => $this->id,
            'id_1c' => $this->id_1c,
            'title' =>  json_decode($this->title),
            'slug' => $this->slug,
        ];*/
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
}
