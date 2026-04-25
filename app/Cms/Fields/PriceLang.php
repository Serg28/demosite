<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Text;

class PriceLang extends Text
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.price_lang', compact('definition', 'field'))->render();
    }

    public function priceOnsite()
    {
        $kurs = setting('kurs') * 1;

        return round($kurs * $this->getTraslationField());
    }
}
