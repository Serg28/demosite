<?php

namespace App\Cms\Tree;

use App\Cms\Tree\Templates\About;
use App\Cms\Tree\Templates\Article;
//use App\Cms\Tree\Templates\Brands;
use App\Cms\Tree\Templates\Comparison;
use App\Cms\Tree\Templates\Contacts;
use App\Cms\Tree\Templates\Credit;
use App\Cms\Tree\Templates\Credits;
//use App\Cms\Tree\Templates\Delivery;
use App\Cms\Tree\Templates\Help;
use App\Cms\Tree\Templates\Politic;
use App\Cms\Tree\Templates\Promotion;
use App\Cms\Tree\Templates\Reviews;
use App\Cms\Tree\Templates\Seocatalog;
use App\Cms\Tree\Templates\SeocatalogLink;
use App\Cms\Tree\Templates\SeocatalogSection;
use App\Cms\Tree\Templates\Service;
//use App\Cms\Tree\Templates\Discountcard;
//use App\Cms\Tree\Templates\Faq;
use App\Cms\Tree\Templates\Main;
use App\Cms\Tree\Templates\News;
use App\Cms\Tree\Templates\Texts;
//use App\Cms\Tree\Templates\Offer;
//use App\Cms\Tree\Templates\Order;
//use App\Cms\Tree\Templates\Politic;
//use App\Cms\Tree\Templates\Promotion;
//use App\Cms\Tree\Templates\Seocatalog;
//use App\Cms\Tree\Templates\SeocatalogLink;
//use App\Cms\Tree\Templates\SeocatalogSection;
use App\Cms\Tree\Templates\Swap;
use App\Cms\Tree\Templates\Universal;
use App\Cms\Tree\Templates\PayDelivery;
//use App\Cms\Tree\Templates\Vacancies;
//use App\Cms\Tree\Templates\Workshop;

use Vis\Builder\Definitions\BaseTree;

class Tree extends BaseTree
{
    public function templates()
    {
        return [
            'main' => Main::class,
            'contacts' => Contacts::class,
            //'article' => Article::class, //Пока вместо него Текстовый
            'news' => News::class,
            //'faq' => Faq::class,
            'about' => About::class,
            //'delivery' => Delivery::class,
            //'offer' => Offer::class,
            'politic' => Politic::class,
            'promotion' => Promotion::class,
            'universal' => Universal::class,
            'text' => Texts::class,
            'payDelivery' => PayDelivery::class,
            'comparison' => Comparison::class,
            //'order_status' => Order::class,
            'service' => Service::class,
            'reviews' => Reviews::class,
            'help' => Help::class,
            'credits' => Credits::class,
            'credit' => Credit::class,
            'swap' => Swap::class,
            //'workshop' => Workshop::class,
            //'brand' => Brands::class,
            //'vacancies' => Vacancies::class,
            //'discount' => Discountcard::class,
            'category_section' => SeocatalogSection::class,
            'category' => Seocatalog::class,
            'category_link' => SeocatalogLink::class,
        ];
    }
}
