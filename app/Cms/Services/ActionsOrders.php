<?php

namespace App\Cms\Services;

use Illuminate\Support\Arr;
use Vis\Builder\Services\Actions as VisActions;

class ActionsOrders extends VisActions
{
    protected $definition;

    private function checkAccess($action)
    {
        if (app('user')->hasAccessActionsForCms($action)) {
            $this->actionsList[$action] = $action;
        }
    }

    public function orderopen()
    {
        $this->actionsList['orderopen'] = 'orderopen';
        $this->checkAccess('update');

        return $this;
    }

    public function fetch($type, $record = null)
    {
        if ($type === 'orderopen') {
            return view('cms.actions.ordertable_open', compact('record'));
        }

        return view("admin::list.actions.{$type}", compact('record'));
    }

    public function list($record)
    {
        $collectionActions = $this->definition->actions()->getActionsAccess();
        $collectionActions = Arr::except($collectionActions, 'insert');

        return view('cms.actions.all', [
            'record' => $record,
            'action' => $this,
            'collectionActions' => $collectionActions,
        ]);
    }
}
