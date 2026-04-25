<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Foreign;

class ForeignTree extends Foreign
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.'.$nameField, compact('definition', 'field'))->render();
    }

    public function getOptions($definition)
    {
        return [];
    }

    public function getValueForList($definition)
    {

        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        if (! $this->value) {
            return null;
        }

        $treeValueThis = $modelRelated::find($this->getId());

        $model = $treeValueThis->menu;

        if ($model) {

            if (is_subclass_of($model, 'App\Models\BaseModel')) {
                return $model->t('title');
            }

            $recurce = $model->getAncestorsAndSelf();

            $collection = [];
            foreach ($recurce as $tree) {
                $collection[] = $tree->t('title');
            }

            if (isset($collection[0])) {
                unset($collection[0]);
            }

            return implode(' / ', $collection);
        }
    }

    public function getFilterInput($list)
    {
        if ($this->filter) {
            $field = $this;
            $filterValue = $this->getFilter($list);
            $definition = $list->getDefinition();

            return view('cms.filters.foreign_tree', compact('field', 'filterValue', 'definition'));
        }
    }
}
