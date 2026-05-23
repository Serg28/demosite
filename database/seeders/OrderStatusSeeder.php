<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['id' => 1,  'title' => ['uk' => 'Новий',                'ru' => 'Новый',                 'en' => 'New'],               'color' => '#6B7280', 'priority' => 1],
            ['id' => 2,  'title' => ['uk' => 'Підтверджено',         'ru' => 'Подтверждён',           'en' => 'Confirmed'],          'color' => '#3B82F6', 'priority' => 2],
            ['id' => 3,  'title' => ['uk' => 'В обробці',            'ru' => 'В обработке',           'en' => 'Processing'],         'color' => '#F59E0B', 'priority' => 3],
            ['id' => 4,  'title' => ['uk' => 'Збирається',           'ru' => 'Собирается',            'en' => 'Picking'],            'color' => '#8B5CF6', 'priority' => 4],
            ['id' => 5,  'title' => ['uk' => 'Готовий до відправки', 'ru' => 'Готов к отправке',      'en' => 'Ready to ship'],      'color' => '#10B981', 'priority' => 5],
            ['id' => 6,  'title' => ['uk' => 'Відправлено',          'ru' => 'Отправлен',             'en' => 'Shipped'],            'color' => '#06B6D4', 'priority' => 6],
            ['id' => 7,  'title' => ['uk' => 'В дорозі',             'ru' => 'В пути',                'en' => 'In transit'],         'color' => '#14B8A6', 'priority' => 7],
            ['id' => 8,  'title' => ['uk' => 'Очікує в пункті',      'ru' => 'Ожидает в пункте',      'en' => 'Awaiting pickup'],    'color' => '#A855F7', 'priority' => 8],
            ['id' => 9,  'title' => ['uk' => 'Доставлено',           'ru' => 'Доставлен',             'en' => 'Delivered'],          'color' => '#22C55E', 'priority' => 9],
            ['id' => 11, 'title' => ['uk' => 'Виконано',             'ru' => 'Выполнен',              'en' => 'Completed'],          'color' => '#16A34A', 'priority' => 11],
            ['id' => 12, 'title' => ['uk' => 'Скасовано клієнтом',   'ru' => 'Отменён клиентом',      'en' => 'Cancelled by client'],'color' => '#EF4444', 'priority' => 12],
            ['id' => 13, 'title' => ['uk' => 'Скасовано магазином',  'ru' => 'Отменён магазином',     'en' => 'Cancelled by shop'],  'color' => '#DC2626', 'priority' => 13],
            ['id' => 14, 'title' => ['uk' => 'Повернення',           'ru' => 'Возврат',               'en' => 'Returned'],           'color' => '#F97316', 'priority' => 14],
            ['id' => 15, 'title' => ['uk' => 'Очікує оплати',        'ru' => 'Ожидает оплаты',        'en' => 'Awaiting payment'],   'color' => '#EAB308', 'priority' => 15],
            ['id' => 16, 'title' => ['uk' => 'Оплачено',             'ru' => 'Оплачен',               'en' => 'Paid'],               'color' => '#15803D', 'priority' => 16],
            ['id' => 17, 'title' => ['uk' => 'Частково оплачено',    'ru' => 'Частично оплачен',      'en' => 'Partially paid'],     'color' => '#CA8A04', 'priority' => 17],
            ['id' => 18, 'title' => ['uk' => 'Помилка оплати',       'ru' => 'Ошибка оплаты',         'en' => 'Payment error'],      'color' => '#B91C1C', 'priority' => 18],
            ['id' => 19, 'title' => ['uk' => 'Відкладено',           'ru' => 'Отложен',               'en' => 'On hold'],            'color' => '#9CA3AF', 'priority' => 19],
            ['id' => 20, 'title' => ['uk' => 'Очікує підтвердження', 'ru' => 'Ожидает подтверждения', 'en' => 'Pending confirmation'],'color' => '#60A5FA', 'priority' => 20],
        ];

        foreach ($statuses as $status) {
            OrderStatus::query()->updateOrCreate(
                ['id' => $status['id']],
                [
                    'title'    => json_encode($status['title'], JSON_UNESCAPED_UNICODE),
                    'color'    => $status['color'],
                    'priority' => $status['priority'],
                ]
            );
        }
    }
}
