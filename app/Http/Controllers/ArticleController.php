<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Contracts\View\View;
use Vis\Builder\TreeController;

class ArticleController extends TreeController
{
    public function index(): View
    {
        return $this->universal();
    }

    public function about(): View
    {
        //return $this->universal();
        $page = $this->node;
        $contactBlock = Tree::find(3)->blocks->firstWhere('template', 'contact_tabs');
        return view('article.about', compact('page', 'contactBlock'));
    }

    public function creditList(): View {
        $page = $this->node;
        $list = $page->load([
            'children.children' => function ($query) {
                $query->active();
            }
        ]);

        return view('credit.index', compact('page', 'list'));
    }

    public function creditPage(): View {
        $page = $this->node;

        return view('credit.page', compact('page'));
    }

    public function contacts(): View
    {
        return $this->universal();
    }

    public function faq(): View
    {
        return $this->universal();
    }

    public function delivery(): View
    {
        $page = $this->node;

        return view('article.delivery', compact('page'));
    }

    public function text(): View
    {
        $page = $this->node;

        return view('article.text', compact('page'));
    }

    public function offer(): View
    {
        $page = $this->node;
        return view('article.offer', compact('page'));
    }

    public function politic(): View
    {
        $page = $this->node;
        return view('article.politic', compact('page'));
    }

    public function vacancies(): View
    {
        $page = $this->node;
        return view('vacancies.index', compact('page'));
    }

    public function universal(): View
    {
        $page = $this->node;

        return view('article.universal', compact('page'));
    }

     public function payDelivery(): View
    {
        $page = $this->node;
        return view('article.payDelivery', compact('page'));
    }

    public function service(): View
    {
        $page = $this->node;

        return view('article.service', compact('page'));
    }

    public function reviews(): View
    {
        $page = $this->node;

        return view('article.reviews', compact('page'));
    }

    public function help(): View
    {
        $page = $this->node;

        return view('article.help', compact('page'));
    }

    public function comparison(): View
    {
        $page = $this->node;

        return view('profile.comparison', compact('page'));
    }

    public function swap(): View
    {
        $page = $this->node;

        return view('article.swap', compact('page'));
    }

    public function workshop(): View
    {
        $page = $this->node;

        return view('workshop.index', compact('page'));
    }

    public function buynewcar(): View
    {
        $page = $this->node;

        return view('buynewcar.index', compact('page'));
    }

    public function sellyourcar(): View
    {
        $page = $this->node;

        return view('sellyourcar.index', compact('page'));
    }



    public function buynewcarModel(): View
    {
        $page = $this->node;

        $page->load(['blocks' => function ($query) {
            $query->active()->with(['modelFacts','picture', 'description', 'blockButtons']);
        }]);

        return view('buynewcar.model', compact('page'));
    }

    public function discount()
    {
        $page = $this->node;
        $form = view('discountcard.partials.form')->render();

        return str_replace('[%form%]', $form, view('discountcard.index', compact('page', 'form')));
    }

    public function getOrderStatus(): View {
        $page = $this->node;
        $order = session('order');

        return view('checkout.order_status', compact('page', 'order'));
    }

}
