<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Демо-значення для всіх налаштувань.
 * Чутливі дані (API-ключі, токени) замінені тестовими заглушками.
 */
class SettingsDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($this->settings() as $s) {
            DB::table('settings')->insert(array_merge([
                'value'                   => '',
                'file'                    => null,
                'check'                   => 0,
                'textarea'                => null,
                'value_languages'         => null,
                'textarea_with_languages' => null,
                'froala_with_languages'   => null,
            ], $s));
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function settings(): array
    {
        return [

            // ── Загальні (General) ────────────────────────────────────────────
            ['group' => 'general', 'type' => 'text',     'slug' => 'currency',             'title' => 'Символ валюти',                   'value' => '₴'],
            ['group' => 'general', 'type' => 'text',     'slug' => 'currency_code',         'title' => 'Код валюти',                      'value' => 'UAH'],
            ['group' => 'general', 'type' => 'checkbox', 'slug' => 'konvertirovat-cenu',    'title' => 'Конвертувати ціну',               'check' => 0],
            ['group' => 'general', 'type' => 'text',     'slug' => 'kurs',                  'title' => 'Курс валюти',                     'value' => '1'],
            ['group' => 'general', 'type' => 'checkbox', 'slug' => 'is-quick-buy-active',   'title' => 'Швидка покупка активна',          'check' => 1],
            ['group' => 'general', 'type' => 'text',     'slug' => 'summa-dlya-dostupnosti-bystrogo-zakaza', 'title' => 'Мін. сума для швидкого замовлення', 'value' => ''],
            ['group' => 'general', 'type' => 'text',     'slug' => 'skidka-po-diskontnoy-karte', 'title' => 'Знижка по дисконтній карті (%)', 'value' => '5'],
            ['group' => 'general', 'type' => 'text',     'slug' => 'maksimalno-kolichestvo-chastey-rassrochki-ili-och', 'title' => 'Макс. кількість частин розстрочки', 'value' => '24'],
            ['group' => 'general', 'type' => 'text',     'slug' => 'external-id-default-unit', 'title' => 'ID одиниці виміру за замовчуванням', 'value' => ''],
            ['group' => 'general', 'type' => 'text',     'slug' => 'characteristic-brand-id',  'title' => 'ID характеристики Бренд',         'value' => ''],

            // ── Контакти (Contact) ────────────────────────────────────────────
            ['group' => 'contact', 'type' => 'text',     'slug' => 'phones-v-header',        'title' => 'Телефони в шапці',               'value' => '+380 (44) 000-00-00'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'telefoni-v-futere',       'title' => 'Телефони в футері',              'value' => '+380 (44) 000-00-00'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'telefon-v-pismah',        'title' => 'Телефон в листах',               'value' => '+380440000000'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'telefony-v-vidzhete-svyazi', 'title' => 'Телефони у віджеті зв\'язку', 'value' => '+380440000000'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'telefoni-kol-centru',     'title' => 'Телефони кол-центру',            'value' => '+380440000000'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'email-v-futere',          'title' => 'Email у футері',                 'value' => 'demo@example.com'],
            ['group' => 'contact', 'type' => 'text',     'slug' => 'footer_address',          'title' => 'Адреса в футері',                'value' => 'м. Київ, вул. Демонстраційна, 1'],
            ['group' => 'contact', 'type' => 'textarea', 'slug' => 'grafik-roboti-shapka',    'title' => 'Графік роботи (шапка)',          'textarea' => 'Пн-Пт: 9:00–18:00'],
            ['group' => 'contact', 'type' => 'textarea', 'slug' => 'footer_all_right',        'title' => 'Текст копірайту в футері',       'textarea' => '© ' . date('Y') . ' Demo Shop. Всі права захищені.'],

            // ── Пошта (Mail) ──────────────────────────────────────────────────
            ['group' => 'mail',    'type' => 'text',     'slug' => 'mail_from_address',       'title' => 'Email відправника',              'value' => 'demo@example.com'],
            ['group' => 'mail',    'type' => 'text',     'slug' => 'mail_from_name',           'title' => 'Ім\'я відправника',              'value' => 'Demo Shop'],
            ['group' => 'mail',    'type' => 'textarea', 'slug' => 'text_in_letter_of_user_unfinished_basket', 'title' => 'Текст листа незакінченого кошика', 'textarea' => 'Ви залишили товари в кошику. Завершіть оформлення замовлення.'],

            // ── SMS (замінені тестові ключі) ──────────────────────────────────
            // УВАГА: замінити реальними даними перед production
            ['group' => 'mail',    'type' => 'text',     'slug' => 'api_key_turbo_sms',       'title' => 'API ключ TurboSMS [TEST]',       'value' => 'test_turbo_sms_api_key'],
            ['group' => 'mail',    'type' => 'text',     'slug' => 'sms_turbo_sms',            'title' => 'Підпис SMS відправника',         'value' => 'DemoShop'],
            ['group' => 'mail',    'type' => 'text',     'slug' => 'message-in-sms',           'title' => 'Шаблон SMS',                    'value' => 'Ваше замовлення #{order_id} прийнято!'],
            ['group' => 'mail',    'type' => 'text',     'slug' => 'viber_turbo_sms',          'title' => 'Viber підпис TurboSMS',          'value' => 'DemoShop'],

            // ── Аналітика (Analytics) ─────────────────────────────────────────
            // Всі вимкнені — встановити реальні ID перед production
            ['group' => 'analytics', 'type' => 'checkbox', 'slug' => 'checkbox_google_analytics',       'title' => 'Google Analytics UA',           'check' => 0],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'code_google_analytics',           'title' => 'Google Analytics UA ID',        'value' => ''],
            ['group' => 'analytics', 'type' => 'checkbox', 'slug' => 'checkbox_google_analytics_four',  'title' => 'Google Analytics 4',            'check' => 0],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'code_google_analytics_four',      'title' => 'Google Analytics 4 ID',         'value' => ''],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'measurement-id-universal-google-analytic-4', 'title' => 'GA4 Measurement ID',  'value' => ''],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'api-secret-universal-google-analytic-4',     'title' => 'GA4 API Secret [TEST]', 'value' => 'test_ga4_api_secret'],
            ['group' => 'analytics', 'type' => 'checkbox', 'slug' => 'checkbox_google_tag_manager',     'title' => 'Google Tag Manager',            'check' => 0],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'code_google_tag_manager',         'title' => 'Google Tag Manager ID',         'value' => ''],
            ['group' => 'analytics', 'type' => 'checkbox', 'slug' => 'checkbox_facebook_pixel',         'title' => 'Facebook Pixel',                'check' => 0],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'code_facebook_pixel',             'title' => 'Facebook Pixel ID',             'value' => ''],
            ['group' => 'analytics', 'type' => 'checkbox', 'slug' => 'checkbox_dynamic_remarketing_google', 'title' => 'Dynamic Remarketing Google', 'check' => 0],
            ['group' => 'analytics', 'type' => 'text',     'slug' => 'code_dynamic_remarketing_google', 'title' => 'Dynamic Remarketing Google ID', 'value' => ''],

            // ── SEO ───────────────────────────────────────────────────────────
            ['group' => 'seo',     'type' => 'file',     'slug' => 'seo_logo',               'title' => 'SEO Logo',                       'file' => ''],
            ['group' => 'seo',     'type' => 'file',     'slug' => 'favicon_ico',             'title' => 'Favicon',                        'file' => ''],
            ['group' => 'seo',     'type' => 'file',     'slug' => 'no-foto',                 'title' => 'Зображення "немає фото"',        'file' => ''],
            ['group' => 'seo',     'type' => 'file',     'slug' => 'page_header_logo',        'title' => 'Логотип (шапка)',                'file' => ''],
            ['group' => 'seo',     'type' => 'file',     'slug' => 'page_footer_logo',        'title' => 'Логотип (футер)',                'file' => ''],
            ['group' => 'seo',     'type' => 'file',     'slug' => 'logo_for_letter',         'title' => 'Логотип для листів',            'file' => ''],
            ['group' => 'seo',     'type' => 'text',     'slug' => 'cross_links_title_categories', 'title' => 'Перехресне посилання: Категорії', 'value' => 'Категорії'],
            ['group' => 'seo',     'type' => 'text',     'slug' => 'cross_links_title_brands_category', 'title' => 'Перехресне посилання: Бренди', 'value' => 'Бренди'],
            ['group' => 'seo',     'type' => 'text',     'slug' => 'cross_links_title_products', 'title' => 'Перехресне посилання: Товари', 'value' => 'Товари'],
            ['group' => 'seo',     'type' => 'text',     'slug' => 'cross_links_title_models', 'title' => 'Перехресне посилання: Моделі', 'value' => 'Моделі'],
            ['group' => 'seo',     'type' => 'checkbox', 'slug' => 'search_anchor_links',     'title' => 'Анкорні посилання в пошуку',    'check' => 0],
            ['group' => 'seo',     'type' => 'text',     'slug' => 'search_anchor_pattern',   'title' => 'Шаблон анкорного посилання',    'value' => ''],

            // ── Соціальні мережі (Social) ─────────────────────────────────────
            ['group' => 'social',  'type' => 'text',     'slug' => 'link-to-viber',           'title' => 'Viber',                          'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link-to-facebook',        'title' => 'Facebook',                       'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link-to-instagram',       'title' => 'Instagram',                      'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link-to-telegram',        'title' => 'Telegram',                       'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link_to_tiktok',          'title' => 'TikTok',                         'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link_to_twitter',         'title' => 'Twitter / X',                    'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'link-to-youtube',         'title' => 'YouTube',                        'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'twitter_account',         'title' => 'Twitter аккаунт (@handle)',      'value' => ''],
            ['group' => 'social',  'type' => 'text',     'slug' => 'count_instagram_feeds',   'title' => 'К-сть постів Instagram',         'value' => '6'],
            ['group' => 'social',  'type' => 'text',     'slug' => 'user_instagram_feeds',    'title' => 'Instagram акаунт (без @)',       'value' => ''],

            // ── Рахунок / Реквізити (Invoice) ────────────────────────────────
            ['group' => 'invoice', 'type' => 'text',     'slug' => 'adres-v-schete',          'title' => 'Адреса в рахунку',               'value' => 'м. Київ, вул. Демонстраційна, 1'],
            ['group' => 'invoice', 'type' => 'text',     'slug' => 'email-v-schete',          'title' => 'Email в рахунку',                'value' => 'demo@example.com'],
            ['group' => 'invoice', 'type' => 'text',     'slug' => 'telefony-v-schete',       'title' => 'Телефони в рахунку',             'value' => '+380440000000'],
            ['group' => 'invoice', 'type' => 'text',     'slug' => 'part_order_request_document_id', 'title' => 'ID документа часткового замовлення', 'value' => ''],
            ['group' => 'invoice', 'type' => 'textarea', 'slug' => 'instrukciya-dlya-oplaty-po-rekvizitam', 'title' => 'Інструкція оплати за реквізитами', 'textarea' => ''],

            // ── Новa Пошта API (замінені тестовим ключем) ────────────────────
            // УВАГА: замінити реальним API ключем перед production
            ['group' => 'api',     'type' => 'text',     'slug' => 'services.np.api_key',     'title' => 'Нова Пошта API ключ [TEST]',     'value' => 'test_np_api_key_replace_me'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'client_np_ref',            'title' => 'Нова Пошта REF відправника',     'value' => ''],
            ['group' => 'api',     'type' => 'text',     'slug' => 'client_np_cityref',        'title' => 'Нова Пошта REF міста відправника', 'value' => ''],
            ['group' => 'api',     'type' => 'text',     'slug' => 'promua-token',             'title' => 'Prom.ua токен [TEST]',           'value' => 'test_promua_token_replace_me'],

            // ── Monobank (замінені тестовими даними) ──────────────────────────
            // УВАГА: замінити реальними даними перед production
            ['group' => 'api',     'type' => 'text',     'slug' => 'monobank-monopay-token',   'title' => 'Monobank Monopay токен [TEST]',  'value' => 'test_monopay_token_replace_me'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'monobank-payparts-storeid', 'title' => 'Monobank Payparts Store ID [TEST]', 'value' => 'test_store_id_replace_me'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'monobank-payparts-secret', 'title' => 'Monobank Payparts Secret [TEST]', 'value' => 'test_payparts_secret_replace_me'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'monobank-payparts-mode',   'title' => 'Monobank Payparts режим',        'value' => 'test'],

            // ── Site API (замінені тестовими даними) ──────────────────────────
            ['group' => 'api',     'type' => 'text',     'slug' => 'site-api-bearer-key',      'title' => 'Site API Bearer ключ [TEST]',   'value' => 'test_site_api_bearer_key'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'site-api-allowed-ip',      'title' => 'Site API дозволені IP',          'value' => '127.0.0.1'],
            ['group' => 'api',     'type' => 'text',     'slug' => 'site-api-docs-allowed-emails', 'title' => 'Site API Docs дозволені emails', 'value' => 'demo@example.com'],

            // ── CRM / Bitrix / SalesDrive (вимкнені, тестові URL) ────────────
            ['group' => 'crm',     'type' => 'checkbox', 'slug' => 'otpravka-v-bitrix',        'title' => 'Відправка в Bitrix',             'check' => 0],
            ['group' => 'crm',     'type' => 'text',     'slug' => 'bitrix-vebhuk',            'title' => 'Bitrix вебхук URL [TEST]',       'value' => 'https://test-bitrix.example.com/webhook/'],
            ['group' => 'crm',     'type' => 'checkbox', 'slug' => 'otpravka-v-sales-drive',   'title' => 'Відправка в SalesDrive',         'check' => 0],
            ['group' => 'crm',     'type' => 'text',     'slug' => 'cabinet-sales-drive',      'title' => 'SalesDrive кабінет URL [TEST]',  'value' => 'https://test-salesdrive.example.com/'],
            ['group' => 'crm',     'type' => 'text',     'slug' => 'export-link-sales-drive',  'title' => 'SalesDrive export URL [TEST]',   'value' => 'https://test-salesdrive.example.com/export'],
            ['group' => 'crm',     'type' => 'text',     'slug' => 'formcode-sales-drive',     'title' => 'SalesDrive form code',           'value' => 'test_form_code'],

            // ── Відгуки (Reviews) ─────────────────────────────────────────────
            ['group' => 'reviews', 'type' => 'text',     'slug' => 'google-reviews-link',      'title' => 'Google Reviews URL',             'value' => ''],
            ['group' => 'reviews', 'type' => 'text',     'slug' => 'google-reviews-img-counter', 'title' => 'Google Reviews к-сть зображень', 'value' => '5'],
            ['group' => 'reviews', 'type' => 'text',     'slug' => 'hotline-reviews-link',     'title' => 'Hotline Reviews URL',            'value' => ''],

            // ── Картка товару ─────────────────────────────────────────────────
            ['group' => 'product', 'type' => 'textarea', 'slug' => 'tekst-garaii-v-kartochke-tovara', 'title' => 'Текст гарантії в картці',  'textarea' => 'Офіційна гарантія виробника.'],
            ['group' => 'product', 'type' => 'textarea', 'slug' => 'tekst-metody-dostavki-v-kartochke-tovara', 'title' => 'Текст доставки в картці', 'textarea' => 'Доставка Новою Поштою по Україні.'],

            // ── Кастомний код ─────────────────────────────────────────────────
            ['group' => 'custom',  'type' => 'textarea', 'slug' => 'js-kod-v-head',            'title' => 'Кастомний JS в <head>',          'textarea' => ''],
            ['group' => 'custom',  'type' => 'textarea', 'slug' => 'chat-code',                 'title' => 'Код чату (Jivosite тощо)',       'textarea' => ''],
            ['group' => 'custom',  'type' => 'textarea', 'slug' => 'body',                      'title' => 'Контент секції body (footer)',   'textarea' => ''],
        ];
    }
}
