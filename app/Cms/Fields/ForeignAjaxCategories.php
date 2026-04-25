<?php

namespace App\Cms\Fields;

use Illuminate\Support\Facades\Cache;
use Vis\Builder\Fields\ForeignAjax;

class ForeignAjaxCategories extends ForeignAjax
{
    public function setValue($item)
    {
        $relation = $item->{$this->options->getRelation()}()
            ->select(['id', "{$this->options->getKeyField()} as name"])
            ->first();

        $this->value = '';

        if ($relation) {
            $this->value = $relation->name;
        }
    }

    public function getValueForExel($definition)
    {
        return $this->getValueForList($definition);
    }

    /*public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {
                $related = $item->{$this->options->getRelation()}()
                    ->select(['id', "{$this->options->getKeyField()} as name"])
                    ->first();

                return [
                    'id' => $related->id,
                    'name' => $related->name,
                ];
            }
        }
    }*/


    public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {
                $related = $item->{$this->options->getRelation()}()
                    ->with('ancestors')->defaultOrder() // Загружаем предков для построения полного пути
                    ->select(['id', "{$this->options->getKeyField()} as name", 'parent_id', 'lft', 'rgt', 'depth'])
                    ->first();

                if ($related) {
                    // Собираем путь от корня к текущей категории
                    $fullPath = $related->ancestors()->defaultOrder()->get()
                            ->filter(function ($ancestor) {
                                return !is_null($ancestor->parent_id); // Исключаем root из пути
                            })
                            ->map(function ($ancestor) {
                                return $ancestor->t('title');
                            })// Меняем порядок на правильный: от корня к текущей категории
                            ->implode(' / ') . ' / ' . $related->name; // Добавляем текущую категорию

                    return [
                        'id' => $related->id,
                        'name' => $fullPath, // Возвращаем полный путь
                    ];
                }
            }
        }

        return null; // Возвращаем null, если ничего не найдено
    }


    public function getValueForList($definition)
    {
        $value = request()->id;
        $model = $definition->model();
        $categoryId = null;

        /*if ($definition->model()) {
            $categoryId = $definition->model()->{$this->options->getRelation()}()->getRelated()->where('title->'.\App::getLocale(), 'like', $this->getValue())->first(['id'])?->id;
        }*/

        if ($definition->model()) {
            $value = $this->getValue();
            if ($value) {
                $categoryId = $definition->model()->{$this->options->getRelation()}()
                    ->getRelated()
                    ->where('title->' . \App::getLocale(), 'like', '%' . $value . '%')
                    ->first(['id'])?->id;
            }
        }

        $categoryId = $categoryId ?? $value ?? null;

        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        if (!$categoryId) {
            return null;
        }

        $treeValueThis = Cache::rememberForever('admin_category_treeValue_' . $categoryId, static function () use ($modelRelated, $categoryId)  {
            return  $modelRelated::with('ancestors')->find($categoryId);
        });

        if (!$treeValueThis) {
            return null; // or handle the case when the record is not found
        }

        $recurce = Cache::rememberForever('admin_category_ancestorsAndSelf_' . $categoryId, static function () use ($treeValueThis, $categoryId) {
            return  $treeValueThis->getAncestorsAndSelf($categoryId, ['id','title']);
        });

        if ($recurce) {

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

    public function getValueForFilter($definition, $id)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        return $modelRelated::find($id)->{$this->options->getKeyField()};
    }

    public function search($definition)
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated->where($keyField, 'like', '%' . $this->convertQuery(request()->q) . '%')
            ->whereNotNull('parent_id'); // Исключаем категории с пустым parent_id

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $nodes = $modelRelated->with('ancestors')
            ->get(['id', 'title', 'parent_id', 'lft', 'rgt', 'depth']);

        $data = [];

        foreach ($nodes as $category) {
            // Собираем путь, начиная с корня и до текущей категории
            $path = $category->ancestors()->defaultOrder()->get()
                    ->filter(function ($ancestor) {
                        return !is_null($ancestor->parent_id)/* && $ancestor->parent_id!==1*/; // Исключаем root из пути
                    })
                    ->map(function ($ancestor) {
                        return $ancestor->t('title');
                    }) // Меняем порядок на правильный: от корня к текущей категории
                    ->implode(' / ') . ' / ' . $category->t('title'); // Добавляем текущую категорию

            $data[] = [
                'id' => $category->id,
                'name' => $path,
            ];
        }

        return [
            'results' => $data,
        ];
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.foreignajaxext', compact('definition', 'field'))->render();
    }

    public function getFilterInput($list)
    {
        $field = $this;
        $filterValue = $this->getFilter($list);
        $definition = $list->getDefinition();

        return view('admin::list.filters.foreignajax', compact('field', 'filterValue', 'definition'));
    }
}
