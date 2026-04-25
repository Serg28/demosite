<?php

namespace App\Providers;

use App\Events\CategorySaved;
use App\Events\CommentCreate;
use App\Events\FeedbackCreate;
use App\Events\FeedSaved;
use App\Events\MenuCatalogSaved;
use App\Events\MenuSaved;
use App\Events\OrderCreate;
use App\Events\QuickOrderCreate;
use App\Events\SeoGroupsSaved;
use App\Listeners\Category\CategoryClearCache;
use App\Listeners\Category\CategoryUniqueSlug;
use App\Listeners\Category\CategoryUpdateTreeDepth;
use App\Listeners\CommentSendNotification;
use App\Listeners\Feed\RegenerateXmlFeedFiles;
use App\Listeners\Menu\MenuClearCache;
use App\Listeners\MenuCatalog\MenuCatalogClearCache;
use App\Listeners\Order\SendMailToAdmin;
use App\Listeners\Order\SendMailToUser;
use App\Listeners\Order\SendToGA4;
use App\Listeners\QuickOrder\SendMailToAdmin as SendMailToAdminQuickOrder;
use App\Listeners\SeoGroups\SeoGroupsClearCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CommentCreate::class => [
            CommentSendNotification::class,
        ],
        OrderCreate::class => [
            //ModifyFields::class, //Модификация полей заказа сразу после его создания
            SendMailToAdmin::class,
            SendMailToUser::class,
            SendToGA4::class,
            //SendMailRequisitesToUser::class, //реквизиты для оплаты на почту
            //SendSms::class,
            //            SendToRabbitMq::class,
            //SendToBitrix::class,
            //SendToSalesDrive::class,
        ],
        QuickOrderCreate::class => [
            //ModifyFields::class, //Модификация полей заказа сразу после его создания
            //SendToBitrix::class,
            SendMailToAdminQuickOrder::class,
            SendToGA4::class,
            //SendToSalesDrive::class
        ],
        FeedbackCreate::class => [
            //SendToBitrixFeedback::class,
            //SendMailToAdminFeedback::class,
            //SendToSalesDriveFeedback::class,
        ],
        FeedSaved::class => [
            RegenerateXmlFeedFiles::class
        ],
        SeoGroupsSaved::class => [
            SeoGroupsClearCache::class
        ],
        CategorySaved::class => [
            CategoryUniqueSlug::class,
            CategoryUpdateTreeDepth::class,
            CategoryClearCache::class
        ],
        MenuSaved::class => [
            MenuClearCache::class
        ],
    ];
}
