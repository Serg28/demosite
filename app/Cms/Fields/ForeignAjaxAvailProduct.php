<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxAvailProduct extends ForeignAjax
{
    public function setValue($item): void
    {
        $relation = $item->{$this->options->getRelation()};
        $this->value = '';
        $availability_info = [];

        if ($relation) {
            $this->value = $relation->getImg(50, 50).
                ' <a href="'.$relation->getUrl().'" target="_blank">'.$relation->t('title').'</a><br>'.
                __cms('Общее наличие:').' <strong>'.$relation->quantity.'</strong>';
            if ($availability_info = json_decode($relation->availability_info, 1)) {
                if (count($availability_info) > 0) {
                    $this->value .= '<br>';
                    foreach ($availability_info as $k => $warehouse) {
                        $this->value .= $warehouse['title'].': <strong>'.$warehouse['quantity'].'</strong><br>';
                    }
                }
            }
        }
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajax_availproducts', compact('definition', 'field'))->render();
    }

    /*public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        $modelRelated = $modelRelated->where(function ($query) use ($keyField): void {
            $query->where($keyField, 'like', '%'.$this->convertQuery(request()->q).'%')->orWhere('code',
                '%'.request()->q.'%');
        })->active()->available();

        $result = $modelRelated->take(10)->get();

        $result = $this->prepareSearchResult($result);

        return [
            'results' => $result,
        ];
    }*/
    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        $result = $modelRelated->where(function ($query) use ($keyField) {
            $query->where(function ($subquery) use ($keyField) {
                $subquery->where($keyField, 'like', '%' . $this->convertQuery(request()->q) . '%');
            })->orWhere(function ($subquery) {
                $subquery->where('code', 'like', '%' . request()->q . '%');
            });
        })->active()->available()->take(10)->get();

        $result = $this->prepareSearchResult($result);

        return [
            'results' => $result,
        ];
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
