<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeoCustomUrlResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $item = $this->seo ?? null;

        return [
            "seo_h1" => optional($item)->t("seo_h1") ?: optional($this)->t("title"),
            "seo_title" => optional($item)->t("seo_title") ?? '',
            "seo_text" => optional($item)->t("seo_text") ?? '',
        ];
    }
}
