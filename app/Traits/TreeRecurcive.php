<?php

namespace App\Traits;

trait TreeRecurcive
{
    //Оригинал
    /*public function getTreeCollection(): array
    {
        $nodes = $this->where('id', '!=', 1)
            ->defaultOrder()
            ->get(['id', 'title'])
            ->toTree();

        $data = [];

        $traverse = function ($categories, $prefix = ' - ') use (&$traverse, &$data): void {
            foreach ($categories as $category) {
                $data[$category->id] = $prefix . ' ' . $category->t('title');
                $traverse($category->children, $prefix . $category->t('title') . ' / ');
            }
        };

        $traverse($nodes);

        return $data;
    }*/

    //Оптимальнее
    public function getTreeCollection(): array
    {
        $nodes = $this->where('id', '!=', 1)
            ->defaultOrder()
            ->select(["id", "title", "parent_id", "lft", "rgt", "depth"])
            ->get(['id', 'title'])
            ->toTree();

        $data = [];

        $traverse = static function ($categories, $prefix = ' ') use (&$traverse, &$data): void {
            foreach ($categories as $category) {
                $data[$category->id] = $prefix.' '.$category->t('title');
                $traverse($category->children, $prefix.'⠀');
            }
        };

        $traverse($nodes);

        return $data;
    }
}
