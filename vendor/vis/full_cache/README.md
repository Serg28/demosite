
В composer.json добавляем в блок require
```json
 "vis/full_cache": "1.*"
```

Выполняем
```json
composer update
```

Добавляем в app.php в массив providers
```php
  Vis\FullCache\FullCacheServiceProvider::class,
```

Добавляем в Kernel.php в $middlewareGroups в массив web
```php
  \Vis\FullCache\Middlewares\FullCacheMiddleware::class,
```

Публикуем конфиг
```php
  php artisan vendor:publish --tag=config_full_cache
```

