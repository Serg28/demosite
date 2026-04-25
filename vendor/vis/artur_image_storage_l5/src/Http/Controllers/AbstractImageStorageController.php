<?php namespace Vis\ImageStorage;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

abstract class AbstractImageStorageController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new $this->model;
    }

    public function fetchIndex()
    {
        $this->setSearchInput();

        $perPage = $this->model->getConfigPerPage();
        $title = __cms("Медиахранилище") . " - " . $this->model->getConfigTitle();
        $prefix = $this->model->getConfigPrefix();
        $relatedEntities = $this->model->getRelatedEntities();

        $data = $this->model->filterSearch()->orderBy('id', 'DESC')->paginate($perPage);

        if (Request::ajax()) {
            $view = "image-storage::" . $prefix . ".partials.content";
        } else {
            $view = "image-storage::" . $prefix . ".index";
        }

        return View::make($view)
            ->with('title', $title)
            ->with('data', $data)
            ->with('relatedEntities', $relatedEntities);
    }

    public function doLoadMoreEndless()
    {
        $perPage = $this->model->getConfigPerPage();
        $prefix  = $this->model->getConfigPrefix();

        Paginator::currentPathResolver(function() {
            return str_replace("/load_more", "", Request::url());
        });

        $data = $this->model->filterSearch()->orderBy('id', 'DESC')->paginate($perPage);

        $html = '';
        foreach ($data as $entity) {
            $html .= View::make('image-storage::' . $prefix . '.partials.single_list')->with('entity', $entity)->render();
        }

        $pagination = View::make('image-storage::partials.pagination')->with('data', $data)->render();

        return Response::json([
            'status' => true,
            'html'   => $html,
            'pagination' => $pagination,
        ]);
    }

    public function doDelete()
    {
        $id = request('id');

        $entity = $this->model->find($id);

        if (!$entity->delete()) {
            return Response::json([
                'status' => false,
                'message' => $entity->getErrorMessage()
            ]);
        }

        return Response::json([
            'id'     => $id,
            'status' => true
        ]);
    }

    public function doDeleteMultiple()
    {
        $idArray = request('idArray', []);

        foreach ($idArray as $key => $id) {

            $entity = $this->model->find($id);

            if (!$entity->delete()) {
                return Response::json([
                    'status' => false,
                    'message' => $entity->getErrorMessage()
                ]);
            }

            $entity->delete();
        }

        return Response::json([
            'status' => true
        ]);
    }

    public function doChangeActivity()
    {
        $idArray  = request('idArray', []);

        $activity = request('activity', 1);

        $this->model->whereIn('id', $idArray)
                    ->update(['is_active' => $activity]);

        return Response::json([
            'status' => true
        ]);
    }

    public function getForm()
    {
        $id = request('id');

        $prefix          = $this->model->getConfigPrefix();
        $fields          = $this->model->getConfigFields();
        $relatedEntities = $this->model->getRelatedEntities();

        $entity  = $this->model->firstOrNew(['id' => $id]);
        
        $html = View::make(
            "image-storage::" . $prefix . ".partials.edit_form",
            compact('entity', 'fields', 'relatedEntities')
        )->render();

        return Response::json([
            'status' => true,
            'html' => $html,
        ]);
    }

    public function doSaveInfo()
    {
        $fields = request()->except('relations');

        $prefix = $this->model->getConfigPrefix();

        $entity = $this->model->firstOrNew(['id' => $fields['id']]);

        $entity->setFields($fields);

        if (!$entity->save()) {
            return Response::json(['status' => false, 'message' => $entity->getErrorMessage()]);
        }

        $html = View::make('image-storage::'. $prefix .'.partials.single_list')->with('entity', $entity)->render();

        return Response::json([
            'html'   => $html,
            'id'     => $entity->id,
            'status' => true,
        ]);
    }

    protected function setSearchInput(){

        $prefix = $this->model->getConfigPrefix();

        if (request()->has('image_storage_filter')) {
            Session::put('image_storage_filter.' . $prefix, request('image_storage_filter', []));
        } elseif (request()->has('forget_filters')) {
            Session::forget('image_storage_filter.' . $prefix);
        }
    }
}
