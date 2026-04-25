<?php

namespace App\Cms\Fields;

use App\Models\User;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;

class ForeignBlog extends Foreign
{
    public function getDataWithWhereAndOrder(Resource $definition)
    {
        return User::select('id', 'first_name as name')->with('roles')->whereHas('roles', function ($query): void {
            $query->where('slug', '=', 'blog');
        })->get();
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('cms.fields.'.$nameField, compact('definition', 'field'))->render();
    }
}
