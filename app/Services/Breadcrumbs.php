<?php

namespace App\Services;

use ArrayObject;

class Breadcrumbs extends ArrayObject
{
    private $breadcrumbs = [];

    /*public function __construct($current)
    {
        $breadcrumbs = $current->getAncestorsAndSelf();

        foreach ($breadcrumbs as $breadcrumb) {
            $this->breadcrumbs[] = [
                'url' => $breadcrumb->getUrl(),
                'title' => $breadcrumb->t('title'),
            ];
        }
    }*/

    public function __construct($current)
    {
        if ($current !== null) {
            $breadcrumbs = $current->getAncestorsAndSelf();

            foreach ($breadcrumbs as $breadcrumb) {
                $this->breadcrumbs[] = [
                    'url' => $breadcrumb->getUrl(),
                    'title' => $breadcrumb->t('title'),
                ];
            }
        }
    }

    public function __get($name)
    {
        if ($name == 'crumbs') {
            return $this->breadcrumbs;
        }

        throw new RuntimeException('Breadcrumbs error!');
    }

    public function add($url = '', $title = ''): void
    {
        if ($url && $title) {
            $this->breadcrumbs[] = [
                'url' => $url,
                'title' => $title,
            ];
        } else {
            $this->breadcrumbs[] = [
                'url' => '',
                'title' => '...',
            ];
        }
    }

    public function pop()
    {
        return array_pop($this->breadcrumbs);
    }
}
