<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Foreign;

class OrderDelivery extends Foreign
{
    protected $options = [];

    protected $transliterationField;

    private $isAction = false;

    private $actionSelect = false;

    private bool $isWarehouseResetFieldsOnChange = false;

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    /*public function getFieldForm($definition)
    {
        return '';
    }*/

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.orderdelivery', compact('definition', 'field'))->render();
    }

    public function getTraslationField(): ?string
    {
        return $this->transliterationField;
    }

    //public function setValue($item): void
    public function getValueForList($definition)
    {
        //$this->allData = $item;
        $item = $this->allData;

        $value = '';

        if ($item->delivery) {
            $value .= '<div>'.$item->delivery->t('title').'</div>';
            //return;
        }
        if ($item->pickUpTheGoods()) {
            $value .= '<div>'.$item->pickUpTheGoods().'</div>';
            //return;
        }

        return $value;
        //$this->value = '<a href="' . route('set_manager', $item) . '" style="color:green; text-decoration: underline">' . __cms('Взять заказ') . '</a>';
    }

    public function action(bool $isAction = true)
    {
        $this->isAction = $isAction;

        return $this;
    }


    public function getAction() : bool
    {
        return $this->isAction;
    }

    public function getActionSelect()
    {
        return $this->actionSelect;
    }

    public function resetWarehouseFieldsOnChange(bool $flag = true)
    {
        $this->isWarehouseResetFieldsOnChange = $flag;

        return $this;
    }

    public function isWarehouseResetFieldsOnChange(): bool
    {
        return $this->isWarehouseResetFieldsOnChange;
    }
}
