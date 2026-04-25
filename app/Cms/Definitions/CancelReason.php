<?php

namespace App\Cms\Definitions;

use App\Models\CancelReason as CancelReasonModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class CancelReason extends Resource
{
    public $model = CancelReasonModel::class;

    public $title = 'Причины расформирования заказа';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
        ];
    }

    public function getCollection($getAllRecords = false)
    {
        $collection = $this->model()->with($this->relations)->whereNotIn('id', [0, 1]);
        $filter = $this->getFilter();
        $orderBy = $this->getOrderBy();
        $perPage = $this->getPerPageThis();
        $collection = $this->getFilterScope($collection);

        if (isset($filter['filter']) && is_array($filter['filter'])) {
            $allFields = $this->getAllFields();

            foreach ($filter['filter'] as $field => $value) {
                if (is_null($value) || $value == '') {
                    continue;
                }

                if ($hasOneRelation = $this->getRelationsHasOne($allFields, $field)) {
                    $collection = $collection->whereHas($hasOneRelation, function ($query) use ($field, $value, $allFields) {
                        $fieldName = $this->getFieldName($allFields, $field);

                        if ($this->isTextField($allFields, $field)) {
                            //   $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');

                            $query->where($fieldName, '=', $value)
                                ->orWhereRaw('LOWER(`'.$fieldName.'`) LIKE ? ', ['%'.trim(mb_strtolower($value)).'%']);
                        } else {
                            $query->where($fieldName, '=', $value);
                        }
                    });
                } else {
                    if (is_array($value)) {
                        if ($value['from'] || $value['to']) {
                            if ($value['from']) {
                                $collection = $collection->where($field, '>=', $value['from']);
                            }

                            if ($value['to']) {
                                $collection = $collection->where($field, '<=', $value['to'].' 23:59:59');
                            }
                        }

                        continue;
                    }

                    $collection = $collection->where(function ($query) use ($field, $value, $allFields) {
                        if ($this->isTextField($allFields, $field)) {
                            //  $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');

                            $query->where($field, '=', $value)
                                ->orWhereRaw('LOWER(`'.$field.'`) LIKE ? ', ['%'.trim(mb_strtolower($value)).'%']);
                        } else {
                            $query->where($field, '=', $value);
                        }
                    });
                }
            }
        }

        if ($getAllRecords) {
            return $collection->orderByRaw($orderBy)->get();
        }

        return $collection->orderByRaw($orderBy)->paginate($perPage);
    }
}
