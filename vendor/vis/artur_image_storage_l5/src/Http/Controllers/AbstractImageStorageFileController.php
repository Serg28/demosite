<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class AbstractImageStorageFileController extends AbstractImageStorageController
{
    public function doUpload()
    {
        $file = request('file');

        $prefix = $this->model->getConfigPrefix();

        $entity = $this->model;

        if (!$entity->setSourceFile($file)) {
            return Response::json(['status' => false, 'message' => $entity->getErrorMessage()]);
        }

        if (!$entity->saveFile()) {
            return Response::json(['status' => false, 'message' => $entity->getErrorMessage()]);
        }

        $html = View::make('image-storage::' . $prefix . '.partials.single_list')->with('entity', $entity)->render();

        return Response::json([
            'status' => true,
            'html'   => $html,
            'id'     => $entity->id
        ]);
    }

    public function doReplaceSingle()
    {
        $file = request('file');
        $size = request('size');
        $id   = request('id');

        $prefix = $this->model->getConfigPrefix();

        $entity = $this->model->find($id);

        if (!$entity->setSourceFile($file)) {
            return Response::json(['status' => false, 'message' => $entity->getErrorMessage()]);
        }

        if (!$entity->saveFileSize($size)) {
            return Response::json(['status' => false, 'message' => $entity->getErrorMessage()]);
        }

        $html = View::make('image-storage::'. $prefix .'.partials.tab_content')
            ->with('entity', $entity)
            ->with('ident',$size)
            ->render();

        return Response::json([
            'status' => true,
            'html'   => $html,
            'src'    => asset($entity->getSource($size))
        ]);
    }

    public function doUpdateNewSize()
    {
        $this->model->doCheckSchemeSizes();
        $this->model->doUpdateSizes();

        return Response::json([
            'status' => true,
        ]);
    }

}
