<?php

return [
    /*
     * Currency symbol shown in price formatting (JS + Blade)
     */
    'currency' => env('SITE_CURRENCY', 'грн'),

    /*
     * BCP 47 locale for Intl.NumberFormat in JS (e.g. 'uk-UA', 'ru-RU', 'en-US')
     */
    'js_locale' => env('SITE_JS_LOCALE', 'uk-UA'),

    /*
     * JSON title field priority — language keys to try in order
     * Matches the structure stored in DB: {"ua":"...", "ru":"..."}
     */
    'title_locales' => ['ua', 'ru', 'en'],

    /*
     * Default locale key used for JSON multilingual fields in DB
     */
    'db_locale' => env('APP_LOCALE', 'ua'),
];
