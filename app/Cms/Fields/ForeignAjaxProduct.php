<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxProduct extends ForeignAjax
{
    public function setValue($item): void
    {
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';

        if ($relation) {
            $this->value = $relation->getImg(50, 50).' <a href="'.$relation->getUrl().'" target="_blank">'.$relation->t('title').'</a>';
        }
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajax', compact('definition', 'field'))->render();
    }

    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        $modelRelated = $modelRelated->where(function ($query) use ($keyField): void {
            $query->where($keyField, 'like', $this->convertQuery(request()->q).'%')->orWhere('code', request()->q);
        })->active();

        $result = $modelRelated->take(10)->get();

        $result = $this->prepareSearchResult($result);

        return [
            'results' => $result,
        ];
    }

    public function getValueForExel($definition)
    {
        return strip_tags($this->getValue());
    }

    private function prepareSearchResult($result)
    {
        return $result->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->t('title'),
                'price' => $item->getPrice(),
                'quantity' => $item->quantity,
            ];
        });
    }
}
