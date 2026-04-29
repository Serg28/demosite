# TODO: Трейты моделей и BaseModel

## BaseModel

Всі моделі (крім Tree і похідних від нього) повинні розширювати `BaseModel`, а не `Model` напряму.

```php
// Зараз (неправильно)
class Product extends Model { ... }
class Category extends Model { ... }

// Має бути
class Product extends BaseModel { ... }
class Category extends BaseModel { ... }
```

Потрібно:
- [ ] Створити `app/Models/BaseModel.php` — порт з linecore-demo
- [ ] Перевести `Product`, `Category`, `Brand` та інші моделі на `BaseModel`

---

## Трейти для Product

| Трейт | Є в demo.loc | Пріоритет | Опис |
|-------|-------------|-----------|------|
| `SlugUrlFieldTrait` | ✅ | — | Локалізований URL |
| `HasTranslations` | ✅ | — | `->t('field')` |
| `HasSeo` | ✅ | — | SEO morph |
| `Sluggable` | ❌ | **HIGH** | Авто-генерація slug при збереженні |
| `PrepareModelFields` | ❌ | **HIGH** | Очищення полів перед збереженням |
| `TranslateTrait` (vendor) | ❌ | MEDIUM | Переклади через пакет CMS (якщо потрібно) |
| `ImagesTrait` (vendor) | ❌ | MEDIUM | Обробка зображень через linecore-cms |
| `RelatedProducts` | ❌ | LOW | Схожі товари |
| `ViewedTrait` | ❌ | LOW | Нещодавно переглянуті |
| `LikeableTrait` | ❌ | LOW | Лайки |
| `HasSmartSorting` | ❌ | LOW | Розумне сортування (ES-специфічне, потрібна адаптація) |

## Трейти для Category

| Трейт | Є в demo.loc | Пріоритет | Опис |
|-------|-------------|-----------|------|
| `SlugUrlFieldTrait` | ✅ (через Tree) | — | Локалізований URL |
| `HasSeo` | ✅ | — | SEO morph |
| `HasTranslations` | ✅ | — | `->t('field')` |
| `Sluggable` | ❌ | **HIGH** | Авто-генерація slug |
| `PrepareModelFields` | ❌ | **HIGH** | Очищення полів |
| `CategorySmartFilterSeoTrait` | ❌ | MEDIUM | SEO для каталогу з фільтрами (адаптувати під PATH-URL) |

---

## Примітки

- `Sluggable` — критично для автоматичного slug при створенні через адмінку
- `PrepareModelFields` — очищає json-поля, trim, null замість пустого рядка
- `CategorySmartFilterSeoTrait` — містить логіку SEO title/description для сторінок з фільтрами; адаптувати під нову PATH-based архітектуру фільтрів
- Tree-моделі (`app/Models/Tree.php`) залишаються без `BaseModel` — у них своя ієрархія
