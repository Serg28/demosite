<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class VideosController extends AbstractImageStorageController
{
    protected $model = Video::class;

    public function doUploadPreviewImage()
    {
        $file = Input::file('file');
        $id   = Input::get('id');

        $video = $this->model->find($id);

        $image = new Image;

        if (!$image->setSourceFile($file)) {
            return Response::json(['status' => false, 'message' => $image->getErrorMessage()]);
        }

        if (!$image->saveFile()) {
            return Response::json(['status' => false, 'message' => $image->getErrorMessage()]);
        }

        $video->setPreviewImage($image->id);

        return Response::json([
            'status' => true,
            'src'    => asset($video->getPreviewImage()),
            'id'     => $video->preview->id
        ]);
    }

    public function doRemovePreviewImage()
    {
        $id = Input::get('id');

        $video = $this->model->find($id);

        $video->unsetPreviewImage();

        return Response::json([
            'status' => true,
            'src'    => asset($video->getPreviewImage()),
        ]);
    }

}
