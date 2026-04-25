<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ImagesController extends AbstractImageStorageFileController
{
    protected $model = Image::class;

    public function doOptimizeImage()
    {
        $size = request('size');
        $id   = (array) request('id');

        foreach ($id as $key => $value) {
            $image = $this->model->find($value);
            $image->optimizeImage($size);
        }

        return Response::json([
            'status' => true,
        ]);
    }
}
