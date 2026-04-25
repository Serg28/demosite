---
name: cms-cards
description: Створення карток (Cards) для дашборду адмінки Linecore Builder CMS. Використовуйте для віджетів статистики, лічильників та агрегованих метрик. Не використовуйте для налаштування переліків Definition. Ключові тригери: dashboard card, виджет, статистика, счетчик.
metadata:
  author: linecore
  version: "1.0"
  package: vendor/linecore/linecore-cms
  updated: "2026-03-31"
---

# Карточки для дашборда в Linecore Builder CMS

## Швидка активація (для запитів будь-якою мовою)
- **Використовувати, коли**: коли потрібно створити KPI/стат-картки на dashboard.
- **RU сигнали**: карточка на дашборд, виджет статистики, счетчик
- **EN signals**: dashboard card, stats widget, value card
- **Не використовувати, коли**: не для фільтрів/полів Definition.

## Коли використовувати
- Додавання виджетов статистики на главную страницу адмінки
- Отображение счетчиков, сумм, последних записів
- Створення дашборда с ключевыми метриками
## Базова структура
**Розташування**: `app/Cms/Cards/`
```php
<?php
namespace App\Cms\Cards;
use Linecore\Cms\Services\Value;
class ExampleCard extends Value
{
  public $title = "Назва карточки";
  public function calculate()
  {
    return $this->count(ModelName::class);
  }
}
```
## Методы Value
### count() - Подсчет записів
public function calculate()
    return $this->count(Order::class);
// С условием
return $this->count(Order::class, function ($query) {
    return $query->where('status', 'completed');
});
### sum() - Сумма значений
    return $this->sum(Order::class, 'total_amount');
return $this->sum(Order::class, 'total_amount', function ($query) {
### avg() - Среднее значение
    return $this->avg(Product::class, 'price');
### max() - Максимальное значение
    return $this->max(Order::class, 'total_amount');
### min() - Минимальное значение
    return $this->min(Product::class, 'price');
## Приклади карточек
### Приклад 1: Количество товаров
use App\Models\Product;
class ProductsCount extends Value
  public $title = "Всего товаров";
    return $this->count(Product::class);
### Приклад 2: Сумма заказов
use App\Models\Order;
class OrdersSum extends Value
  public $title = "Сумма заказов";
    return $this->sum(Order::class, "total_amount", function ($query) {
      return $query->where("status", "completed");
    });
### Приклад 3: Новые заказы за сегодня
use Carbon\Carbon;
class TodayOrders extends Value
  public $title = "Заказов сегодня";
    return $this->count(Order::class, function ($query) {
      return $query->whereDate("created_at", Carbon::today());
### Приклад 4: Процент конверсии
class ActiveProductsPercent extends Value
  public $title = "Активных товаров %";
    $total = Product::count();
    $active = Product::where("is_active", true)->count();
    if ($total == 0) {
      return 0;
    }
    return round(($active / $total) * 100, 1) . "%";
### Приклад 5: Последний заказ
class LastOrder extends Value
  public $title = "Последний заказ";
    $order = Order::latest("created_at")->first();
    if (!$order) {
      return "—";
    return $order->total_amount .
      " грн (" .
      $order->created_at->format("d.m.H:i") .
      ")";
## Подключение карточек
### В Definition
public function cards()
    return [
        ProductsCount::class,
        OrdersSum::class,
        TodayOrders::class,
    ];
### Регистрация в CMS (глобально)
// В каком-то сервис-провайдере или при загрузке
$cards = [
  "products" => ProductsCount::class,
  "orders" => OrdersSum::class,
];
## Расширенные возможности
### Кастомний URL
  public $link = "/admin/products"; // Куда ведет карточка
### Иконка
  public $icon = "box"; // Иконка для карточки
### Форматирование
    $sum = $this->sum(Order::class, "total_amount");
    return number_format($sum, 0, ",", " ") . " грн";
## Важливі нюанси
1. **Namespace**: Карточки располагаются в `App\Cms\Cards\`
2. **Наслідування**: Наследуйтесь от `Linecore\Cms\Services\Value`
3. **Назва**: Заголовок в `$title`
4. **Значение**: Возвращайте значение в `calculate()`
5. **Форматирование**: Форматируйте значение для отображения (суммы, даты)
## Перегляд существующих карточек
```bash
ls -la app/Cms/Cards/
## Приклад комплексного дашборда
use App\Models\{Product, Order, User};
class DashboardCards
  public static function all(): array
      ProductsCount::class,
      ProductsActive::class,
      OrdersCount::class,
      OrdersSum::class,
      TodayOrders::class,
      UsersCount::class,
      NewUsers::class,
