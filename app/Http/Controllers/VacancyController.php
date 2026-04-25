<?php
namespace App\Http\Controllers;

class VacancyController extends BaseController {
    public function index(){
        return View('vacancies.index');
    }

    public function page(){
        return View('vacancies.page');
    }
}