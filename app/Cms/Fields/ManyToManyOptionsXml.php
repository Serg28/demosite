<?php

namespace App\Cms\Fields;

use App\Models\Feed;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ManyToMany;

class ManyToManyOptionsXml extends ManyToMany
{
    protected $onlyForm = false;

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.many_to_many_options_xml', compact('definition', 'field'))->render();
    }

    public function getValueForList($definition): string
    {
        $feeXml = Feed::find($this->getId());

        $data = [];

        foreach ($feeXml->options as $category) {
            $data[] = $category->t('title');
        }

        return implode(', ', $data);
    }

    public function save($collectionArray, $model)
    {
        $model->{$this->options->getRelation()}()->detach();

        if (is_array($collectionArray) && $collectionArray[0]) {
            $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionArray);
        }
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {
            $tableRelateModel = $definition->model()->find(request()->id)
                ->{$this->options->getRelation()}()->getRelated()->getTable();

            $selected = $definition->model()->find(request()->id)->{$this->options->getRelation()}()
                ->select(["{$tableRelateModel}.id", "{$tableRelateModel}.{$this->options->getKeyField()} as name"])
                ->get();

            $result = [];

            foreach ($selected as $item) {
                $result[$item->id] = $item->t('name');
            }

            return $result;
        }

        return [];
    }
}
