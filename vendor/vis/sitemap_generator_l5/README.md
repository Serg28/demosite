Model based sitemap generator
Generated Sitemap will be available at http(s)://yoursite.com/sitemap.xml

Execute
```json
    composer require "vis/sitemap_generator_l5":"1.*"
```

Add SitemapGeneratorServiceProvider to ServiceProviders in config/app.php
```php
   Vis\SitemapGenerator\SitemapGeneratorServiceProvider::class,
```

Publish sitemap config
```php
    php artisan vendor:publish --provider="Vis\SitemapGenerator\SitemapGeneratorServiceProvider" --force
```

Add your models\custom urls to config at app/config/sitemap-generator/sitemap.php

Option to enabled\disable Sitemap generation, if disabled shows 404 error
```php
    'is_enabled' => true,
```

Option to enabled\disable multi lang links in sitemap
```php
    'is_multi_language' => true,
```

Models
Short example that will use default options
```php
    'models' => [
        'Tree',
    ],
```

Full example of possible options
```php
    'models' => [
        'Tree_1' => [
            //If this param is set model name will be taken from here rather then from array key
            //This allows to have multiple request to single model without overriding results
            'model' => "Tree",

            // Valid values are "always|hourly|daily|weekly|monthly|yearly|never"
            'changefreq' => "daily",

            // Valid values range from 0.0 to 1.0.
            'priority'   => 0.7,

            // Method that will be called upon model to get Url of entity
            // Default(if removed) - "getUrl" or set your method name
            'url_method' => "getUrl",

            // Optional property. Default(if removed) - "updated_at", set false to disable or set your field name
            'lastmod_field' => "updated_at",

            // Optional property. Default(if removed) - "is_active", set false to disable or set your field name
            'active_field'  => "is_active",

            // Optional property. Allows to specify query, can be removed if not required
            'additional_where' => [
                'template' => [
                    'sign'  => '!=',
                    'value' => 'main'
                ],
            ],
        ],
    ],
```

Custom links
Short example that will use default options
```php
'custom_links' => [
        '/',
    ],
```

Full example of possible options
```php
    'custom_links' => [
        '/' => [
            //If this param is set url be taken from here rather then from array key
            'url' => "/",

            // Valid values are "always|hourly|daily|weekly|monthly|yearly|never"
            'changefreq' => "daily",

            // Valid values range from 0.0 to 1.0.
            'priority'   => 1,

            // Valid values is anything parsable by strtotime method
            'lastmod'    => "2017-02-20 13:32:09",
        ],
    ],
```
