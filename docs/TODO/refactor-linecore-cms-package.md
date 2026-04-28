# Рефакторинг пакету linecore/linecore-cms

## TranslateTrait — підтримка JSON cast (PHP array)

**Файл у пакеті:** `vendor/linecore/linecore-cms/src/Http/Traits/TranslateTrait.php`

**Проблеми поточної версії:**
1. `t()` мутує `$this->$ident` через `preg_replace`/`str_replace` — модифікує атрибут моделі в пам'яті (побічний ефект, баг).
2. Не підтримує PHP array — якщо поле має cast `'json'`, `json_decode()` на масиві повертає `null`.
3. Немає типів параметра та повернення.
4. `t_htmlfix()` має баг: звертається до `$fieldArray` та `$lang` поза їх scope.

---

### Готовий код для заміни

```php
<?php

namespace Linecore\Cms\Helpers\Traits;

use Illuminate\Support\Facades\App;

trait TranslateTrait
{
    public function t(string $ident): string
    {
        $value = $this->{$ident};
        $lang = App::getLocale();

        // Підтримка PHP array (Laravel 'json' cast)
        if (is_array($value)) {
            return (string) ($value[$lang] ?? '');
        }

        // Legacy: JSON рядок без cast
        $raw = preg_replace("/[\r\n]+/", '\\r\\n', (string) $value);
        $raw = str_replace("\t", '\t', $raw);
        $decoded = json_decode($raw);

        return (string) ($decoded->{$lang} ?? '');
    }

    public function t_htmlfix(string $ident): string
    {
        $lang = App::getLocale();
        $value = $this->{$ident};

        // Отримуємо сирий HTML для поточної локалі
        if (is_array($value)) {
            $content = (string) ($value[$lang] ?? '');
        } else {
            $raw = preg_replace("/[\r\n]+/", '\\r\\n', (string) $value);
            $raw = str_replace("\t", '\t', $raw);
            $decoded = json_decode($raw);
            $content = (string) ($decoded->{$lang} ?? '');
        }

        if ($content === '') {
            return '';
        }

        $encoding = mb_detect_encoding($content) ?: 'UTF-8';
        $doc = new \DOMDocument('', $encoding);
        @$doc->loadHTML(
            '<html><head><meta http-equiv="content-type" content="text/html; charset='
            . $encoding . '"></head><body>' . trim($content) . '</body></html>'
        );

        $nodes = $doc->getElementsByTagName('body')->item(0)->childNodes;
        $html = '';
        for ($i = 0, $len = $nodes->length; $i < $len; $i++) {
            $html .= $doc->saveHTML($nodes->item($i));
        }

        // Прибираємо зайві <br> всередині таблиць та списків
        $html = preg_replace_callback('/<table[^>]*>.*?<\/table>/s', function ($match) {
            return preg_replace('/<br\s*\/?>/i', '', $match[0]);
        }, $html);
        $html = str_replace(['</tr><br /><tr>', '</tr><br><tr>', '</tr><br> <tr>', '</tr><br/> <tr>'], '</tr><tr>', $html);
        $html = str_replace(['</li><br /><li>', '</li><br><li>'], '</li><li>', $html);

        return $html;
    }
}
```

---

### Після оновлення пакету в demo.loc

1. Видалити `app/Models/Traits/HasTranslations.php`
2. У моделях (`Product`, `Category`, `Characteristic`) замінити `use HasTranslations` на `use \Linecore\Cms\Helpers\Traits\TranslateTrait` (або додати до BaseModel, якщо він буде)
3. Дублювання зникне

**Пріоритет:** Середній — поточне рішення в demo.loc через `HasTranslations` працює коректно, але це дублювання.
