<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class OrderContacts extends Virtual
{
    protected $options = [];

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getFieldForm($definition)
    {
        return '';
    }

    public function setValue($item): void
    {
        $this->allData = $item;

        $this->value = '';

        if ($item->last_name) {
            $this->value .= '<div style="text-align:left">'.implode(' ', [$item->last_name, $item->first_name, $item->patronimyc]).'</div>';
            //return;
        }
        if ($item->email) {
            $this->value .= '<div style="text-align:left">'.$item->email.'</div>';
        }
        if ($item->phone) {
            $this->value .= '<div style="text-align:left">'.$item->phone.'</div>';
            //return;
        }
        if ($item->comment) {
            $this->value .= '<div style="text-align:left;font-style: italic">'.$item->comment.'</div>';
            //return;
        }

        if ($this->value) {
            return;
        }

        //$this->value = '<a href="' . route('set_manager', $item) . '" style="color:green; text-decoration: underline">' . __cms('Взять заказ') . '</a>';
    }
}
