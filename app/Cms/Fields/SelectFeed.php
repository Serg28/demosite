<?php

namespace App\Cms\Fields;

use App\Models\Feed;
use Vis\Builder\Fields\Select;

class SelectFeed extends Select
{
    public function getFieldForm($definition): string
    {
        $field = $this;

        return view('admin::form.fields.select', compact('definition', 'field'))->render();
    }

    public function getValueForList($definition): string
    {
        return '<a target="_blank" href="'.$this->getLinkForFeed().'">'.$this->getLinkForFeed().'</a>';
    }

    private function getLinkForFeed(): string
    {
        $feeXml = Feed::find($this->getId());

        return $feeXml->getUrl(); //ссылка на динамический файл (из коробки)
    }
}
