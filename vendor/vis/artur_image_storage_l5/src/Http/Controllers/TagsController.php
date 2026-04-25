<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class TagsController extends AbstractImageStorageController
{
    protected $model = Tag::class;

    public function doRelateToTags($type)
    {
        $idTags  = Input::get('idTags', []);
        $idArray = Input::get('idArray', []);

        foreach ($idTags as $key => $id) {
            $tag = $this->model->find($id);
            $tag->relateToTag($idArray, $type);
        }

        return Response::json([
            'status' => true
        ]);
    }

}
