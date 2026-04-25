<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Foreign;

class ForeignFOP extends Foreign
{
    public $value = '';

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.foreign_fop', compact('definition', 'field'))->render();
    }

    /*public function getOptions($definition)
    {
        return [];
    }*/

    /*public function getValueForList($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        if (!$this->value) {
            return null;
        }

        $treeValueThis = $modelRelated::find($this->getId());
        $model = $treeValueThis->menu;

        if ($model) {
            $recurce = $model->getAncestorsAndSelf();

            $collection = [];
            foreach ($recurce as $tree) {
                $collection[] = $tree->t('title') ;
            }

            if (isset($collection[0])) {
                unset($collection[0]);
            }

            return implode(' / ', $collection);
        }
    }*/

    public function getFilterInput($list)
    {
        if ($this->filter) {
            $field = $this;
            $filterValue = $this->getFilter($list);
            $definition = $list->getDefinition();

            return view('admin::list.filters.foreign', compact('field', 'filterValue', 'definition'));
        }
    }

    public function fastSave($definition, $request)
    {
        $model = $definition->model()->find($request['pk']);
        $model->{$request['ident']} = $request['value'];
        $model->save();

        $definition->clearCache();
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $optionsArray = $this->getOptions($definition);

        if ($this->fastEdit) {
            $idRecord = $this->getId();
            $field = $this->getNameFieldInBd();

            return view('admin::list.fast_edit.select', compact('idRecord', 'value', 'field', 'optionsArray'));
        }

        $definition = $this->getDefinition($definition);
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $record = $modelRelated::select(['id', $this->options->getKeyField().' as name']);

        $recordThis = $record->rememberForever()
            ->cacheTags($this->getCacheArray($definition, $modelRelated))
            ->find($value);

        return optional($recordThis)->name;
    }
}
