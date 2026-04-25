<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Field;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Virtual;

class OrderContactsExt extends Virtual
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

    /*public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('admin::form.fields.text', compact('definition', 'field'))->render();
    }*/

    /*public function setValue($item): void
    {
        $this->allData = $item;

        $this->value = '';

        if ($item->last_name) {
            $this->value .= '<div style="text-align:left">' . implode(' ', [$item->last_name, $item->first_name, $item->patronimyc ] ) . '</div>';
            //return;
        }
        if ($item->email) {
            $this->value .= '<div style="text-align:left">' . $item->email . '</div>';
        }
        if ($item->phone) {
            $this->value .= '<div style="text-align:left">' . $item->phone . '</div>';
            //return;
        }
        if ($item->comment) {
            $this->value .= '<div style="text-align:left;font-style: italic">' . $item->comment . '</div>';
            //return;
        }

        if ($this->value) {
            return;
        }
    }*/
    public function getValueForList($definition)
    {
        $item = $this->allData;

        $value = '';

        if ($item->first_name) {
            $value .= '<div style="text-align:left">'.implode(' ', [$item->last_name, $item->first_name, $item->patronimyc]).'</div>';
            //return;
        }
        if ($item->email) {
            $value .= '<div style="text-align:left">'.$item->email.'</div>';
        }
        if ($item->phone) {
            $value .= '<div style="text-align:left">'.$item->phone.'</div>';
            //return;
        }
        if ($item->comment) {
            $value .= '<div style="text-align:left;font-style: italic">'.$item->comment.'</div>';
            //return;
        }
        if ($item->prom_id) {
            $value .= '<div style="text-align:left;padding: 1px 5px 0px 5px;font-size: 12px;background:#7e808512;width: fit-content;margin-top: 5px;color:#d33e33;">Prom #'.$item->prom_id.'</div>';
        } else {
            $text = ($item->is_quick) ? __cms('В 1 клик') : __cms('С сайта');
            $value .= '<div style="text-align:left;padding: 1px 5px 0px 5px;font-size: 12px;background:#7e808512;width: fit-content;margin-top: 5px;color:#d33e33;">'.$text.'</div>';
        }

        return $value;
    }
}
