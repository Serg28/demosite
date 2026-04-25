<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeoCustomUrlCatalogResource extends JsonResource
{

    public $page;
    public $filter;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param mixed $page
     * @param mixed $filter
     */
    public function __construct($resource, $filter)
    {
        parent::__construct($resource);
        $this->filter = $filter;
    }


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
            "seo_title" => optional($this)->getSeoTitle($this->filter) ?? '',
            "seo_text" => optional($item)->t("seo_text") ?? '',
        ];
    }
}
