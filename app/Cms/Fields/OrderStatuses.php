<?php

namespace App\Cms\Fields;

use App\Models\ComplectStatus;
use App\Models\OrderStatus;
use App\Models\PayStatus;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Fields\Virtual;

class OrderStatuses extends Virtual
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

        $status = $item->status;
        $complectation = $item->complectation;
        $paymentstatus = $item->paymentstatus;

        if ($status) {
            $this->value .= '<div class="status-badge" style="background-color: '.$status->color.'">'.$status->t('title').'</div>';
        }
        if ($complectation) {
            $this->value .= '<div class="status-badge" style="background-color: '.$complectation->color.'">'.$complectation->t('title').'</div>';
        }
        if ($paymentstatus) {
            $this->value .= '<div class="status-badge" style="background-color: '.$paymentstatus->color.'">'.$paymentstatus->t('title').'</div>';
        } else {
            $this->value .= '<div class="status-badge" style="background-color: #ff0000">'.__cms('Неоплаченный').'</div>';
        }
        if ($this->value) {
            return;
        }
    }


    public function getFilterInput($list)
    {
        $definition = $list->getDefinition();
        $field = $this;
        $statuses = OrderStatus::orderPriority()->get();
        $complectations = ComplectStatus::orderPriority()->get();
        $paymentStatuses = PayStatus::orderPriority()->get();
        $filtersCurrent = $definition->getFilter()['filter'] ?? '';

        return view('cms.filters.order_statuses',
            compact('definition', 'field', 'statuses', 'complectations', 'paymentStatuses', 'filtersCurrent'));
    }

    /*
    public function search($definition)
    {
        $modelRelated = $definition->model();
        $modelRelated = OrderStatus::where('title', 'like', "%".request()->q . "%");

        $result = $modelRelated->select(DB::raw('id, title as name'))->take(10)->get()->toArray();

        return [
            'results' => $result
        ];
    }*/
}
