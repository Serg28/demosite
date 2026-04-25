<?php

namespace App\Cms\Fields;

use Vis\Builder\Fields\Virtual;

class CommentableModel extends Virtual
{
    public function getValueForList($definition): string
    {
        $commentable = $this->allData->commentable;
        return view('cms.fields.commentablemodel', compact('definition', 'commentable'))->render();
    }

    public function getFieldForm($definition)
    {
        $commentable = $this->allData->commentable;
        //return view('cms.fields.commentablemodel', compact('definition', 'commentable'))->render();
        return '';
    }
}
