<?php

namespace App\Models;

class TreeMenu extends MenuBase
{
    protected $table = 'tb_tree_menus';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function getUrl($locale = ''): string
    {
        if ($this->menu_type && $this->menu) {
            return $this->menu->getUrl($locale);
        }

        if ($this->t('url')) {
            return $this->t('url');
        }

        if ($this->t('url_external')) {
            return $this->t('url_external');
        }

        return '';
    }
}
