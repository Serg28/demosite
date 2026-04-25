<?php

namespace App\Cms\Fields;

use App\Enums\FeedTypeEnum;
use App\Models\Category;
use App\Models\Feed;
use Illuminate\Support\Facades\Cache;
use Vis\Builder\Fields\ManyToManyMultiSelect;

class ManyToManyCategories extends ManyToManyMultiSelect
{
    protected $onlyForm = false;

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.many_to_many_categories', compact('definition', 'field'))->render();
    }

    public function getOptions($definition): array
    {
        //return (new Category())->getTreeCollection();
        return Cache::rememberForever('admin_category_tree', static function () {
            return (new Category())->getTreeCollection();
        });
    }

    public function getOptionsSelected($definition): array
    {
        if (request()->id) {
            //$selected = $definition->model()->find(request()->id)->{$this->options->getRelation()}()->get();
            //$selected = $definition->model()->select(['id','title'])->find(request()->id)->{$this->options->getRelation()}()->get(['id','title']);

            $selected = $definition->model()->select(['id', $this->options->getKeyField() .' as title'])->find(request()->id)->{$this->options->getRelation()}()->lazy();

            $data = [];

            foreach ($selected as $item) {
                //$data[$item->id] = $this->getBreadcrumbs($item->getAncestorsAndSelf());
                $data[$item->id] = $this->getBreadcrumbs($item->getAncestorsAndSelf(request()->id, ['id','title']));
            }

            return $data;
        }

        return [];
    }

    public function getValueForList($definition): string
    {
        $feeXml = Feed::find($this->getId());

        if ($feeXml->type === FeedTypeEnum::all_products->name) {
            return 'Все категории';
        }

        $data = [];

        foreach ($feeXml->categories as $category) {
            $data[] = $category->t('title');
        }

        return implode(', ', $data);
    }

    private function getBreadcrumbs($data): string
    {
        $collection = [];

        foreach ($data as $item) {
            if ($item->id !== 1) {
                $collection[] = $item->t('title');
            }
        }

        return implode(' / ', $collection);
    }
}
