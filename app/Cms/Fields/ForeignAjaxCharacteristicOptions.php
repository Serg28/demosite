<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Foreign;

class ForeignAjaxCharacteristicOptions extends Foreign
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajaxcharacteristicoptions', compact('definition', 'field'))->render();
    }

    public function search($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        $result = $modelRelated::where('title->ua', 'like', request('q').'%')->where('characteristic_id', request('characteristic_id'))
            ->orWhere('title->ru', 'like', request('q').'%')->where('characteristic_id', request('characteristic_id'))
            ->get();

        $result->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->t('title'),
            ];
        });

        return [
            'results' => $result,
        ];
    }

    public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {
                $related = $item->{$this->options->getRelation()};

                return [
                    'id' => $related->id,
                    'name' => $related->t('title'),
                ];
            }
        }
    }
}
