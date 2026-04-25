<?php

namespace App\Cms;

use Vis\Builder\Setting\AdminBase;

class Admin extends AdminBase
{
    protected $logoUrl = '/cms_files/logo-w.svg?1';

    protected $faviconUrl = '/cms_files/favicon.ico';

    protected $css = [
        '/packages/vis/builder/css/your_style.css',
        '/packages/vis/builder/admin/css/custom-admin-styles.css?1'
    ];

    protected $js = [
        '/packages/vis/builder/admin/js/app.js?1',
        '/packages/vis/builder/admin/js/orders-admin-js.js?1',
        '/packages/vis/builder/admin/js/custom-admin-js.js?1',
        '/packages/vis/builder/admin/js/imagegallery-popup-admin.js',
        '/packages/vis/image-storage/js/image_storage.js'
    ];

    public function login()
    {
        return Login::class;
    }

    public function menu()
    {
        return [

            [
                'title' => 'Рабочий стол',
                'icon' => 'chart-line',
                'link' => '/dashboard',
            ],

            [
                'title' => 'Структура сайта',
                'icon' => 'sitemap',
                'link' => '/tree',
            ],

            [
                'title' => 'Меню',
                'icon' => 'chart-network',
                'link' => '/menu_footer_header',
                'submenu' => [
                    [
                        'title' => 'Меню Хедер',
                        'link' => '/menu_header',
                    ],
                    [
                        'title' => 'Меню Боковое',
                        'link' => '/menu_sidebar',
                    ],
                    [
                        'title' => 'Меню Футера',
                        'link' => '/menu_footer',
                    ],
                    [
                        'title' => "Меню Каталог",
                        'link'  => '/menu_catalog',
                    ],

                    /*[
                        'title' => "SEO-Каталог",
                        'link'  => '/menu_seocatalog',
                    ],*/
                ],
            ],

            [
                'title' => 'Новости',
                'icon' => 'building',
                'link' => '/news',
                'submenu' => [
                    [
                        'title' => 'Новости',
                        'link' => '/news',
                    ],
                    [
                        'title' => 'Категории',
                        'link' => '/tree?node=10',
                    ],
                    [
                        'title' => 'Теги',
                        'link' => '/tags',
                    ],

                ],
            ],

            [
                'title' => 'Товары',
                'icon' => 'money-bill',
                'link' => '/money',
                'submenu' => [
                    [
                        'title' => 'Товары',
                        'link' => '/products',
                    ],
                    [
                        'title' => 'Категории',
                        'link' => '/categories',
                    ],
                    [
                        'title' => 'Метки',
                        'link' => '/labels',
                    ],
                    [
                        'title' => 'Бренды',
                        'link' => '/brands',
                    ],
                    [
                        'title' => 'Гарантии',
                        'link' => '/guarantee',
                    ],
                    [
                        'title' => 'Статусы',
                        'link' => '/product_status',
                    ],
                    [
                        'title' => 'Связанные товары',
                        'link' => '/related_products_by_characteristic',
                    ],
                    [
                        'title' => 'Заказы товаров на складах',
                        'link' => '/warehouse_order_product',
                    ],
                    [
                        'title' => 'XML фиды',
                        'link' => '/xml_feeds',
                    ],
                ],
            ],

            [
                'title' => 'Характеристики',
                'icon' => 'cog',
                'link' => '/characteristics_menu',
                'submenu' => [
                    [
                        'title' => 'Группы',
                        'link' => '/characteristic_group_names',
                    ],
                    [
                        'title' => 'Название',
                        'link' => '/characteristics',
                    ],
                    [
                        'title' => 'Значения',
                        'link' => '/characteristic_options',
                    ],
                ],
            ],

            [
                'title' => 'Заказы',
                'icon' => 'shopping-cart',
                'link' => '/orders_all',
                'submenu' => [
                    [
                        'title' => 'Заказы',
                        'link' => '/orders',
                    ],
                    [
                        'title' => 'Быстрые заказы',
                        'link' => '/orders_quick',
                    ],

                    [
                        'title' => 'Prom заказы',
                        'link' => '/orders_prom',
                    ],

                    [
                        'title' => 'Брошенные корзины',
                        'link' => '/unfinished_baskets',
                    ],

                    [
                        'title' => 'Сообщить про наличие товара',
                        'link' => '/availability_order',
                    ],

                    [
                        'title' => 'Отслеживание цен на товар',
                        'link' => '/follow_prices',
                    ],

                    [
                        'title' => 'Платежи',
                        'link' => '/order_payments_page',
                    ],

                    [
                        'title' => 'Статусы заказов',
                        'link' => '/orders_status',
                    ],
                    [
                        'title' => 'Промокода',
                        'link' => '/promo_codes',
                    ],
                    [
                        'title' => 'Дисконтные карты',
                        'link' => '/discount_cards',
                    ],
                ],
            ],

            [
                'title' => 'Склады',
                'icon' => 'warehouse-alt',
                'link' => '/warehouses',
            ],

            [
                'title' => 'Акции',
                'icon' => 'gifts',
                'link' => '/promotions',
            ],
            [
                'title' => 'Сообщения',
                'icon' => 'comments',
                'link' => '/messages',
                'submenu' => [
                    [
                        'title' => 'Обратная связь',
                        'link' => '/feedback',
                    ],
                    [
                        'title' => 'Комментарии',
                        'link' => '/comments',
                    ],
                    [
                        'title' => 'Подписки',
                        'link' => '/subscriptions',
                    ],
                    [
                        'title' => 'Вопросы/ответы',
                        'link' => '/faqs',
                    ],

                ],
            ],


//            [
//                'title' => 'Обратная связь',
//                'icon' => 'comment-dots',
//                'link' => '/feedback',
//            ],
//
//            [
//                'title' => 'Комментарии',
//                'icon' => 'comments',
//                'link' => '/comments',
//            ],
//
//            [
//                'title' => 'Подписки',
//                'icon' => 'rss',
//                'link' => '/subscriptions',
//            ],

            [
                /*'title' => 'Медиахранилище',
                'icon' => 'images',
                'link' => 'image_storage',
                'submenu' => [
                    [
                        'title' => 'Изображения',
                        'link' => '/image_storage/images',
                    ],
                    [
                        'title' => 'Галерея',
                        'link' => '/image_storage/galleries',
                    ],
                ],*/
                'title' => 'Медиахранилище',
                'icon' => 'images',
                'link' => '/filemanager',
            ],

            [
                'title' => 'Настройки',
                'icon' => 'cog',
                'link' => '/settings_block',
                'submenu' => [
                    [
                        'title' => 'Переменные',
                        'link' => '/settings_all',
                        'submenu' => [
                            [
                                'title' => 'Все',
                                'link' => '/settings',
                            ],
                            [
                                'title' => 'Сервисные сообщения',
                                'link' => '/settings_messages',
                            ],
                            [
                                'title' => 'Социальные сети',
                                'link' => '/settings_social',
                            ],
                            [
                                'title' => 'Настройки почты',
                                'link' => '/settings_mail',
                            ],
                            [
                                'title' => 'Контакты',
                                'link' => '/settings_contacts',
                            ],
                            [
                                'title' => 'Установка кода',
                                'link' => '/settings_code',
                            ],
                            [
                                'title' => 'Валюта',
                                'link' => '/settings_currency',
                            ],
                            [
                                'title' => 'Уведомления про заказ',
                                'link' => '/settings_order',
                            ],
                            [
                                'title' => 'Интернет маркетинг',
                                'link' => '/settings_internet_marketing',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Каталог и товары',
                        'link' => '/settings_catalog_product',
                    ],
                    [
                        'title' => 'Контроль изменений',
                        'link' => '/revisions',
                    ],
                    [
                        'title' => 'Редиректы',
                        'link' => '/redirects',
                    ],

                    [
                        'title' => 'SEO',
                        'link' => '/seo',
                        'submenu' => [
                            [
                                'title' => 'SEO-теги для URL',
                                'link' => '/seo_urls',
                            ],
                            [
                                'title' => 'SEO-теги для страниц',
                                'link' => '/seo_groups',
                            ],
                        ]
                    ],

                    [
                        'title' => 'Способы оплаты',
                        'link' => '/pay_methods',
                    ],

                    [
                        'title' => 'Платежные системы',
                        'link' => '/checkouts',
                    ],

                    [
                        'title' => 'Юридичні особи',
                        'link' => '/legal_entities_recipients',
                    ],

                    [
                        'title' => 'Накопительные скидки',
                        'link' => '/cumulative_discounts',
                    ],
                    [
                        'title' => 'Интеграции',
                        'link' => '/integrations_all',
                        'submenu' => [
                            [
                                'title' => 'Все',
                                'link' => '/integrations',
                            ],
                            [
                                'title' => 'Google reCAPTCHA',
                                'link' => '/integrations_recaptcha',
                            ],
                            [
                                'title' => 'Google',
                                'link' => '/integrations_google',
                            ],
                            [
                                'title' => 'Facebook',
                                'link' => '/integrations_facebook',
                            ],
                            [
                                'title' => 'Sales Drive',
                                'link' => '/integrations_sales_drive',
                            ],
                            [
                                'title' => 'Битрикс 24',
                                'link' => '/integrations_bitrix',
                            ],
                            [
                                'title' => 'Turbo SMS',
                                'link' => '/integrations_turbo_sms',
                            ],
                            [
                                'title' => 'Prom.ua',
                                'link' => '/integrations_promua',
                            ],
                            [
                                'title' => 'Новая Почта',
                                'link' => '/integrations_novaposhta',
                            ],
                            [
                                'title' => 'LiqPay',
                                'link' => '/integrations_liqpay',
                            ],
                            [
                                'title' => 'Приват ОЧ',
                                'link' => '/integrations_privatpayparts',
                            ],
                            [
                                'title' => 'Монобанк',
                                'link' => '/integrations_monopayparts',
                            ],
                            [
                                'title' => 'WayForPay',
                                'link' => '/integrations_wayforpay',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Словари',
                        'link' => '/dictionaries',
                        'submenu' => [
                            [
                                'title' => 'Причины расформирования заказ',
                                'link' => '/cancel_reason',
                            ],

                        ],
                    ],
                    [
                        'title' => 'API сайта',
                        'icon' => 'laptop-code',
                        'link' => '/settings_site_api',
                    ],
                    [
                        'title' => 'Информационная полоса ',
                        'icon' => 'laptop-code',
                        'link' => '/informations_board',
                    ],
                ],
            ],
            [
                'title' => 'Доставка',
                'icon' => 'truck-couch',
                'link' => '/deliveries_all',
                'submenu' => [
                    [
                        'title' => 'Способы доставки',
                        'link' => '/deliveries',
                    ],
                    [
                        'title' => 'Города',
                        'link' => '/cities',
                    ],
                    [
                        'title' => 'Регион',
                        'link' => '/regions',
                    ],
                    [
                        'title' => 'Тип населенного пункта',
                        'link' => '/settlements',
                    ],
                    [
                        'title' => 'Отделения Новой почты',
                        'link' => '/np_warehouse',
                    ],
                    [
                        'title' => 'Отделения Укрпочты',
                        'link' => '/ukrposhta_warehouse',
                    ],
                    [
                        'title' => 'Отделения Justin',
                        'link' => '/justin_warehouse',
                    ],
                    [
                        'title' => 'Отделения Meest',
                        'link' => '/meest_warehouse',
                    ],
                ],
            ],

            [
                'title' => 'Переводы',
                'icon' => 'globe-europe',
                'link' => '/translations/phrases',
                'submenu' => [

                    [
                        'title' => 'Языки сайта',
                        'link' => '/languages',
                    ],

                    [
                        'title' => 'Переводы сайта',
                        'link' => '/translations/phrases',
                    ],

                    [
                        'title' => 'Переводы CMS',
                        'link' => '/translations_cms/phrases',
                    ],

                ],
            ],

            [
                'title' => 'Упр. пользователями',
                'icon' => 'user',
                'link' => '/users_group',
                'submenu' => [
                    [
                        'title' => 'Пользователи',
                        'link' => '/users',
                    ],

                    [
                        'title' => 'Группы',
                        'link' => '/groups',
                    ],

                ],
            ],
        ];
    }
}
