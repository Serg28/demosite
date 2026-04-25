<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\Cache;
use Vis\Builder\Fields\Foreign;

class ForeignTreeCategory extends Foreign
{
    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        //Скрываем поле для корневой категории
        if(empty($field->allData->parent_id) && empty($field->allData->depth)) {
            //return '';
        }

        return view('cms.fields.'.$nameField, compact('definition', 'field'))->render();
    }

    /*
    // Оригинал
    public function getOptions($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $nodes = $modelRelated::where('id', '!=', 1)->defaultOrder()->get()->toTree();

        $data = [];

        $traverse = function ($categories, $prefix = ' - ') use (&$traverse, &$data): void {
            foreach ($categories as $category) {
                $data[$category->id] = $prefix.' '.$category->t('title');
                $traverse($category->children, $prefix.$category->t('title').' / ');
            }
        };

        $traverse($nodes);

        return $data;
    }
    */

    //Быстрее
    public function getOptions($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        return Cache::rememberForever('admin_category_tree', static function () use ($modelRelated) {
            return $modelRelated->getTreeCollection();
        });
    }
    //---

    public function getValueForList($definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();

        if (! $this->value) {
            return null;
        }

        /*
        // медленно
        $treeValueThis = $modelRelated::->find($this->value);
        if (!$treeValueThis) {
            return null; // or handle the case when the record is not found
        }
        $recurce = $treeValueThis->getAncestorsAndSelf();
        */

        /* быстрее */
        $treeValueThis = Cache::rememberForever('admin_category_treeValue_' . $this->value, function () use ($modelRelated)  {
            return $modelRelated::select(['id', 'title'])->with('ancestors')->find($this->value);
        });

        if (!$treeValueThis) {
            return null; // or handle the case when the record is not found
        }

        $recurce = Cache::rememberForever('admin_category_ancestorsAndSelf_' . $this->value, function () use ($treeValueThis) {
            return $treeValueThis->getAncestorsAndSelf($this->value, ['id','title']);
        });
        /*---*/

        $collection = [];
        foreach ($recurce as $tree) {
            $collection[] = $tree->t('title');
        }

        if (isset($collection[0])) {
            unset($collection[0]);
        }

        return implode(' / ', $collection);
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
