<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Text;

class UserInOrder extends Text
{
    public function getValueForList($definition)
    {
        $data = $this->getAllData();

        if ($data->user_id) {
            return '<a target="_blank" href="/admin/users?id='.$data->user_id.'">'.$this->getValue().'</a>';
        }

        return $this->getValue();
    }

    public function getFieldForm($definition)
    {
        $field = $this;

        return view('admin::form.fields.text', compact('definition', 'field'))->render();
    }
}
