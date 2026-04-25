<?php

namespace App\Http\Controllers;

class AnaliticController extends BaseController {

    public function setListName() {
        $list_name = request('list');
        session('analitic_list_name', $list_name);
        return $list_name;
    }

    public function getListName() {
        return session()->get('analitic_list_name');
    }

}
