<?php

if (!function_exists('inflection')) {
    /**
     * inflection() - функция для получения склонения слова в зависимости от числа.
     *
     * Функция возвращает правильное склонение слова для переданного числа, учитывая особенности русского языка.
     *
     * @param int $number Число, для которого нужно получить склонение.
     * @param array $variants Массив строк с тремя формами склонения для чисел 1, 2-4 и 5 и более.
     *
     * @return string   Склоненная форма слова в зависимости от числа.
     */
    /*
     * Пример использования:
     * $count = 42;
     * $wordVariants = ['товар', 'товара', 'товаров'];
     * $result = inflection($count, $wordVariants);
     * echo "У вас есть $count $result.";
     * // Вывод: "У вас есть 42 товара."
     */
    function inflection($number, $variants)
    {
        if ($number % 100 >= 11 && $number % 100 <= 20) {
            return $variants[2];
        } else {
            switch ($number % 10) {
                case 1:
                    return $variants[0];
                case 2:
                case 3:
                case 4:
                    return $variants[1];
                default:
                    return $variants[2];
            }
        }
    }
}

if (!function_exists('data_forget')) {
    /**
     * Remove / unset an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param string|array|int|null $key
     * @return mixed
     */
    function data_forget(&$target, $key)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*' && Arr::accessible($target)) {
            if ($segments) {
                foreach ($target as &$inner) {
                    data_forget($inner, $segments);
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments && Arr::exists($target, $segment)) {
                data_forget($target[$segment], $segments);
            } else {
                Arr::forget($target, $segment);
            }
        } elseif (is_object($target)) {
            if ($segments && isset($target->{$segment})) {
                data_forget($target->{$segment}, $segments);
            } elseif (isset($target->{$segment})) {
                unset($target->{$segment});
            }
        }

        return $target;
    }
}

/**
 * Получает текущий URL, с возможностью исключить GET-параметры.
 *
 * Если текущий URL содержит 'livewire/update', функция вернёт значение
 * заголовка `referer`. В противном случае вернётся текущий URL.
 *
 * @param bool $withoutQuery Указывает, нужно ли вернуть URL без GET-параметров.
 *                           По умолчанию — false.
 * @return string|null Возвращает текущий URL или путь без GET-параметров, если указан флаг $withoutQuery.
 */
if (!function_exists('currentUrl')) {
    function currentUrl(bool $withoutQuery = false): string|null
    {
        $currentUrl = request()->fullUrl();
        $refererUrl = request()->header('referer') ?? '';

        // Если URL содержит `livewire/update`, используем referer
        $url = str_contains($currentUrl, 'livewire/update') ? $refererUrl : $currentUrl;

        // Возвращаем путь без GET-параметров, если установлен флаг
        //return $withoutQuery ? parse_url($url, PHP_URL_PATH) : $url;
        return $withoutQuery ? (str_contains($url, '?') ? explode('?', $url)[0] : $url) : $url;
    }
}

//Возвращает текущий URL - только часть path (без домена и get-параметров), даже если вызов произошел из livewire-компонента.
if (!function_exists('currentUrlPath')) {
    function currentUrlPath(): string|null
    {
        $url = currentUrl();

        // Функция для извлечения пути из URL
        $getPath = static function ($url) {
            $parsedUrl = parse_url($url);
            return isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '';
        };

        return $url ? $getPath($url) : null;
    }
}

/**
 * Получает значение указанного GET-параметра из текущего URL.
 *
 * @param string $param Имя GET-параметра, значение которого нужно получить.
 * @return string|null Возвращает значение параметра или null, если параметр отсутствует.
 */
if (!function_exists('getQueryParam')) {

    function getQueryParam(string $param): ?string
    {
        // Получаем текущий URL с параметрами
        $url = currentUrl();

        // Извлекаем строку запроса (GET-параметры) из URL
        $query = parse_url($url, PHP_URL_QUERY);

        // Если параметры существуют, разбираем их
        if ($query) {
            parse_str($query, $queryParams);
            return $queryParams[$param] ?? null;
        }

        return null;
    }
}


if (!function_exists('processImages')) {
    /**
     * Обработка изображений в HTML-контенте, замена путей и атрибутов изображений.
     *
     * Эта функция принимает HTML-контент, находит все теги изображений (img) и выполняет следующие действия:
     * 1. Заменяет путь у изображения на "/img/trans.svg" и устанавливает оригинальный путь в качестве атрибута data-src.
     * 2. Добавляет класс "lazy" к изображению. Если уже существует какой-то класс, "lazy" добавляется к нему. Если класс отсутствует, создается и добавляется "lazy".
     *
     * @param string $content HTML-контент для обработки.
     *
     * @return string Обновленный HTML-контент с измененными путями и атрибутами изображений.
     */
    function processImages($content)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(
            "<html><head>" .
            '<meta http-equiv="content-type" content="text/html; charset=UTF-8"></head><body>' .
            trim($content) .
            "</body></html>"
        );

        $images = $dom->getElementsByTagName("img");

        foreach ($images as $image) {
            // Замена пути у картинки и атрибута
            $image->setAttribute("data-src", $image->getAttribute("src"));
            $image->setAttribute("src", "/img/trans.svg");

            // Добавление класса lazy
            $class = $image->getAttribute("class");
            $class = $class ? $class . " lazy" : "lazy";
            $image->setAttribute("class", $class);
        }

        // Извлечение обновленного HTML
        $updatedContent = "";
        $bodyNodes = $dom->getElementsByTagName("body")->item(0)->childNodes;
        $len = $bodyNodes->length;
        for ($i = 0; $i < $len; $i++) {
            $updatedContent .= $dom->saveHTML($bodyNodes->item($i));
        }

        return $updatedContent;
    }

    if (! function_exists('getSiteLanguages')) {

        function getSiteLanguages()
        {
            return cache()->remember('languages_of_site', now()->addHours(1), function () {
                return languagesOfSite();
            });
        }
    }
}
