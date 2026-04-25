<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Vis\Builder\TreeController;

class ContactsController extends TreeController
{
    public function index(): View
    {
        $page = $this->node;
        $contacts = $page->blocks->firstWhere('template', 'contact_tabs');

        $_cont = $page->blocks->first();
        $_phone = $_cont->contactRubric->skip(0)->take(1)[0];
        $_social = $_cont->contactRubric->skip(1)->take(2)[1];
        $_email = $_cont->contactRubric->skip(1)->take(2)[2];
        $phone = [
            'title' => $_phone->t('title'),
            'picture' => $_phone->picture,
            'list' => $_phone->contact_manies
        ];
        $social = [
            'title' => $_social->t('title'),
            'list' => $_social->contact_manies
        ];
        $email = [
            'title' => $_email->t('title'),
            'picture' => $_email->picture,
            'list' => $_email->contact_manies
        ];

        return view('contacts.index', compact('page', 'contacts', 'phone', 'social', 'email'));
    }
}
