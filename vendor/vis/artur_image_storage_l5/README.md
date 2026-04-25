# Image Storage
Пакет-медиахранилище для Laravel 5.4 предназначенный работы с изображением, видео и документами.

Разделы
1. [Установка](#Установка)
2. [VIS-CMS](#VIS-CMS)
3. [Настройка конфигов](#Настройка-конфигов)
    1. [Конфиг изображений](#Конфиг-изображений)
    2. [Конфиг документов](#Конфиг-документов)
    3. [Конфиг видео](#Конфиг-видео)
        * [Конфиг видео API](#Конфиг-видео-api)
4. [Спецификация и примеры](#Спецификация-и-примеры)
    1. [Общая спецификация](#Общая-спецификация)
    3. [Использование изображений](#Использование-изображений)
    4. [Использование фотогалереи](#Использование-фотогалереи)
    5. [Использование документов](#Использование-документов)
    6. [Использование видео](#Использование-видео)
        * [Использование видео API](#Использование-видео-api)
    7. [Использование видеогалереи](#Использование-видеогалереи)
    8. [Использование тэгов](#Использование-тэгов)
5. [Кэширование](#Кэширование)
6. [Особенности управление в VIS CMS](#Особенности-управление-в-vis-cms)
    1. [Общее управление](#Общее-управление)
    2. [Общее управление для изображений, видео и документов](#Общее-управление-для-изображений-видео-и-документов)
        1. [Управление изображениями](#Управление-изображениями)
        2. [Управление видео](#Управление-видео)
        3. [Управление документами](#Управление-документами)
    3. [Общее управление для фотогалерей и видеогалерей](#Общее-управление-для-фотогалерей-и-видеогалерей)

## Установка

Выполняем
```json
    composer require "vis/image_storage_l5":"1.*"
```

Добавляем в config\app.php в массив providers
```php
    Vis\ImageStorage\ImageStorageServiceProvider::class,
```

Выполняем миграцию таблиц
```json
   php artisan migrate --path=vendor/vis/image_storage_l5/src/Migrations
```

Публикуем config, js, css, images
```json
    php artisan vendor:publish --provider="Vis\ImageStorage\ImageStorageServiceProvider" --force
```

## VIS-CMS
В файле config/builder/admin.php в массив menu в настройки добавляем
```php
    array(
      'title' => 'Медиахранилище',
      'icon'  => 'picture-o',
      'check' => function() {
          return Sentinel::hasAccess('admin.image_storage.view');
      },
      'submenu' => array(
          array(
              'title' => "Изображения",
              'link'  => '/image_storage/images',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
          array(
              'title' => "Галереи",
              'link'  => '/image_storage/galleries',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
          array(
              'title' => "Видео",
              'link'  => '/image_storage/videos',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
          array(
              'title' => "Видеогалереи",
              'link'  => '/image_storage/video_galleries',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
          array(
              'title' => "Документы",
              'link'  => '/image_storage/documents',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
          array(
              'title' => "Теги",
              'link'  => '/image_storage/tags',
              'check' => function() {
                  return Sentinel::hasAccess('admin.image_storage.view');
              }
          ),
      )
    ),
```

Добавляем права доступа в config/builder/tb-definitions/groups.php и добавляем их к группам.
```php
    'Медиахранилище' => array(
        'admin.image_storage.view'   => 'Просмотр',
        'admin.image_storage.create' => 'Создание',
        'admin.image_storage.update' => 'Редактирование',
        'admin.image_storage.delete' => 'Удаление',
    ),
```


## Настройка конфигов
Все конфиги содержат 3 основных настройки:

Настройка title указывающее на имя раздела отображаемое в VIS-CMS
```php
    'title' => "Галереи",
```

Настройка per_page указывающее количество записей отображаемых на странице  в VIS-CMS
```php
    'per_page' => 20,
```

Настройка fields соддержащее набор полей, которые будут выводиться в форме редактирования записи. </br>
Значения: text, textarea,checkbox, select, datetime. Определяются как и в VIS-CMS. </br>
Поддерживается динамическое создание новых полей и табов
```php
    'fields' => array(
        ...
    ),
```

### Конфиг изображений
Настройки валидации загружаемых изображений. В ошибки автоматически подставляются значения 'max_size' и 'extension_list'
```php
    'size_validation' => array(
        'enabled' => true,
        'max_size' => '1500000',
        'error_message' => "Превышен максимальный размер изображения в [size] MB"
    ),
    'extension_validation' => array(
        'enabled' => true,
        'allowed_extensions' => array('png', 'jpg', 'jpeg'),
        'error_message' => "Допустимы только изображения форматов: [extension_list]"
    ),
```

Настройка качества для загружаемых JPG изображений.
Значение: 0-100
```php
    'quality' => 85,
```

Настройка использования класса Vis\Builder\OptimizationImg для оптимизации загруженных изображений.</br>
Значение: true\false
```php
    'optimization' => true,
```

Настройка использования исходного названия изображения для поля title.</br>
Значение: true\false
```php
    'source_title' => true,
```

Настройка хранения метаданных изображения в базе данных.</br>
Применятся функция exif_read_data, данные хранятся в формате json.</br>
Значение: true\false
```php
    'store_exif' => true,
```

Настройка удаления файлов изображений при удалении сущности изображения.</br>
Значение: true\false

```php
    'delete_files' => true,
```
Настройка переименования файлов изображений при переименовании сущности изображения.</br>
Значение: true\false

```php
    'rename_files' => true,
```

Настройка отображения кнопки генерации новых размеров для записей.</br>
Применяется при необходимости сгененировать новый размер для уже существующих записей. </br>
Значение: true\false

```php
    'display_generate_new_size_button' => true,
```

Настройка генерируемых размеров изображений. </br>
Позволяет при загрузке изображений автоматически генерировать изображение в других размерах. </br>
Использует пакет для работы с изображениями [Intervention](http://image.intervention.io/). </br>
Модифицировать изображение можно в помощью настройки Modify принимающей параметры Intervention
```php
    'sizes' => array(
        'source' => array(
            'caption' => 'Оригинал',
            'default_tab' => true,
        ),
        'cms_preview' => array(
            'caption' => 'Превью в ЦМС',
            'default_tab' => false,
            'modify' => array(
                'fit' => array(160, 160, function (\Intervention\Image\Constraint $constraint) {
                    $constraint->upsize();
                }),
            ),
        ),
```

### Конфиг документов
Все параметры настройки аналогичны изображениям, кроме </br>
Настройка позволяющая хранить несколько файлов под одной сущности "документа". </br>
Может быть использовано когда необходимо выводить разные файлы на разных языках сайта</br>
При загрузке документа во все размеры устанавливается ссылка на исходный файл, которую потом можно заменить
```php
    'sizes' => array(
        'source' => array(
            'caption' => 'Основной файл',
            'default_tab' => true,
        ),
        'ua' => array(
            'caption' => 'Файл на укр',
            'default_tab' => false,
        ),
        'en' => array(
            'caption' => 'Файл на англ',
            'default_tab' => false,
        ),

    ),
```

### Конфиг видео
В массиве настройки fields два обязательных поля отвечающие за сервис и идентификатор видео
```php
    'api_provider' => array(
        'caption' => 'Видео сервис',
        'type' => 'select',
        'options' => config('image-storage.video_api.provider_names')
    ),
    'api_id' => array(
        'caption' => 'Идентификатор видео',
        'type' => 'text',
        'field' => 'string',
        'placeholder' => 'Идентификатор видео',
    ),
```

#### Конфиг видео API
Настройка управляющая отправление запрос к видео API, которые требуют ключи </br>
Значение: true\false
```php
    'enabled' => true,
```

Настройка времени кэширования ответа от видео API</br>
Значение: x, 0 (прим. 0 - вечность), false
```php
    'cache_minutes' => 60,
```

Настройка автоматического заполнения полей title&description из ответа видео API</br>
Значение: true\false
```php
    'set_data' => true,
```

Настройка имен провайдеров предоставляющих видео API. </br>
Выводится в select создании\редактировании видео
```php
    'provider_names' => array(
        'youtube' => 'Youtube',
        'vimeo'   => 'Vimeo',
    ),
```   

Настройка провайдеров предоставляющих видео API
```php
    'providers' => array(
        'youtube' => array(
         ...
        ),
        'vimeo' => array(
        ...
        ),
     )
```

Настройки каждого из видео API провайдеров</br>

Настройка проверки существования видео на сервисе (не требует ключа API)
```php
    'video_existence_url' => '',
```

Настройка ссылки на изображение-превью на сервисе (не требует ключа API)
```php
    'preview_url' => '',
```

Настройка качества изображения-превью на сервисе
```php
    'preview_quality' => '',
```

Настройка ссылки на просмотр видео (не требует ключа API)
```php
    'watch_url' => '',
```

Настройка ссылки на встраиваемое видео (не требует ключа API)
```php
    'embed_url' => '',
```

Настройка ссылки на сервис API
```php
    'api_url' => '',
```

Настройка данных которые будут запрошены у сервиса API
```php
    'api_part' => '',
```

Настройка ключа для подключения к сервису API
```php
    'api_key' => '',
```

## Спецификация и примеры
### Общая спецификация
Для подключения необходимого нужно определить его вызов в начале своего класса.
```php
    use Vis\ImageStorage\Gallery;
    use Vis\ImageStorage\Image;
    use Vis\ImageStorage\Tag;
    use Vis\ImageStorage\VideoGallery;
    use Vis\ImageStorage\Video;
    use Vis\ImageStorage\Documents;
```    

Ко всем моделям можно применять стандартный принцип написания запросов, поскольку они наследуются от модели Eloquent. </br>
Так же все модели используют трейты VIS CMS \Vis\Builder\Helpers\Traits\TranslateTrait и \Vis\Builder\Helpers\Traits\SeoTrait </br>

Для всех записей генерируется уникальных слаг, его значение можно получить с помощью метода
```php
    public function getSlug()
```

Общие scope фильтры для всех моделей (\Models\Traits\FilterableTrait.php)</br>
Фильтр сортировки по id.
```php
    public function scopeOrderId(Builder $query, $order = "desc")
```  

Фильтр выведения только активных записей.
```php
    public function scopeActive(Builder $query)
```

Фильтр выведения записей согласна массива активностей </br>
Значение: массив $activity[0,1]
```php
   public function scopeFilterByActivity(Builder $query, array $activity = [])
```   

Фильтр по slug записи
```php
    public function scopeSlug(Builder $query, $slug = '')
``` 

Фильтр по title записи
```php
    public function scopeFilterByTitle(Builder $query, $title = '')
```   

Фильтр по дате создания записи. </br>
Значение: массив $date['from' => '', to => '']
```php
    public function scopeFilterByDate(Builder $query, array $date = [])
```   

Фильтр по связанным тэгам. </br>
Значение: массив $tags[$idTags]
```php
    public function scopeFilterByTags(Builder $query, array $tags = [])
```

Eloquent связь с тэгами. Получает все связанные тэги с сущностями
```php
    public function tags()
```
  
### Использование изображений
Eloquent связь с галереями. Получает все связанные галереи с изображением
```php
    public function galleries()
```

Фильтр изображений по галереям.</br>
Значение: массив $galleries[$idGalleries]
```php
    public function scopeFilterByGalleries(Builder $query, array $galleries = [])
```

Метод получения ссылки на изображение по именному роуту. Роут необходимо определить самостоятельно
```php
    public function getUrl()
    {
        return route("vis_images_show_single", [$this->getSlug()]);
    }
```

Наследует от абстракции src/Models/AbstractImageStorageFile.php следующие методы </br>
Получить путь изображения в указанном размере</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getSource($size = 'source')
```

Получить разрешение файла изображения</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileExtension($size = 'source')
``` 

Получить название файла изображения</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileName($size = 'source')
```  

Получить размер файла изображения</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileSize($size = 'source')
```  

Получить mime-type файла изображения</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileMimeType($size = 'source')
```  

### Использование фотогалереи
Eloquent связь с изображения. Получает все связанные изображения с галереей
```php
    public function images()
```

Фильтр галерей по наличию изображений
```php
    public function scopeHasImages(Builder $query)
```

Фильтр галерей по наличию активных изображений
```php
    public function scopeHasActiveImages(Builder $query)
```

Метод получения ссылки на галерею по именному роуту. Роут необходимо определить самостоятельно
```php
    public function getUrl()
    {
        return route("vis_galleries_show_single", [$this->getSlug()]);
    }
```

Метод получения превью-изображения для галереи  </br>
Значение: один из указаных в конфиге изображений размеров, по умолчанию - cms_preview
```php
    public function getGalleryPreviewImage($size = 'cms_preview')
```

### Использование документов
Наследует от абстракции src/Models/AbstractImageStorageFile.php следующие методы </br>
Получить путь документа в указанном размере</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getSource($size = 'source')
```

Получить разрешение файла документа</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileExtension($size = 'source')
``` 

Получить название файла документа</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileName($size = 'source')
```  

Получить размер файла документа</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileSize($size = 'source')
```  

Получить mime-type файла документа</br>
Значение: один из указаных в конфиге размеров, по умолчанию - source
```php
    public function getFileMimeType($size = 'source')
```  

### Использование видео
Eloquent связь с изображением-превью. Получает объект установленного изображения-превью
```php
    public function preview()
```

Eloquent связь с изображения. Получает все связанные изображения с галереей
```php
    public function videoGalleries()
```

Связь с API провайдером. Получает объект API provider в зависимости от типа видео.
```php
    public function api()
```

Фильтр видео по видеогалереям галереям.</br>
Значение: массив $galleries[$idGalleries]
```php
    public function scopeFilterByVideoGalleries(Builder $query, array $galleries = [])
```

Метод получения id видео</br>
```php
    public function getSource()
```

Метод получения ссылки на видео по именному роуту. Роут необходимо определить самостоятельно
```php
    public function getUrl()
    {
        return route("vis_videos_show_single", [$this->getSlug()]);
    }
```

Метод получения ссылки на изображение-превью видео. </br>
Получает или установленное изображение-превью или обращается API за изображением.
```php
    public function getPreviewImage($size = 'source')
```

#### Использование видео API
Видео API реализует интерфейс /Models/Interfaces/VideoAPIInterface.php и имеет следующие методы </br>

Метод получения ссылки на видео
```php
    public function getWatchUrl(array $urlParams);
```

Метод получения ссылки на встраиваемое видео 
```php
    public function getEmbedUrl(array $urlParams);
```

Метод получения ссылки на изображени-превью из API
```php
    public function getPreviewUrl();
```

Метод получения всех данных о видео из API
```php
    public function getApiResponse();
```

Метод получения названия видео из API
```php
    public function getTitle();
```

Метод получения описание видео из API
```php
    public function getDescription();
```

Метод получения количества просмотров видео из API
```php
    public function getViewCount();
```

Метод получения количества лайков видео из API
```php
    public function getLikeCount();
```

Метод получения количества дизлайков видео из API
```php
    public function getDislikeCount();
```

Метод получения количества favorite для видео  из API
```php
    public function getFavoriteCount();
```

Метод получения количества комментариев для видео  из API
```php
    public function getCommentCount();
```

### Использование видеогалереи
Eloquent связь с видео. Получает все связанные видео с видеогалереей
```php
    public function videos()
```

Фильтр видеогалерей по наличию изображений
```php
    public function scopeHasVideos(Builder $query)
```

Фильтр видеогалерей по наличию активных видео
```php
    public function scopeHasActiveVideos(Builder $query)
```

Метод получения ссылки на видеогалерею по именному роуту. Роут необходимо определить самостоятельно
```php
    public function getUrl()
    {
        return route("vis_video_galleries_show_single", [$this->getSlug()]);
    }
```

### Использование тэгов
Eloquent связь с изображения. Получает все связанные изображения с тэгом
```php
    public function images()
```

Eloquent связь с документами. Получает все связанные изображения с тэгом
```php
    public function documents()
```

Eloquent связь с видео. Получает все связанные изображения с тэгом
```php
    public function videos()
```

Eloquent связь с галереями. Получает все связанные изображения с тэгом
```php
    public function galleries()
```

Eloquent связь с видеогалереями. Получает все связанные изображения с тэгом
```php
    public function videoGalleries()
```

## Кэширование
Медиахранилище использует ряд тегов для работы кэширования. </br>
При внесении измений в записи медиахранилища происходит автоматическое сбрасывание существующего кэша связанного с этими тегами.</br>
Перечень тэгов для каждой из сущностей:
* image-storage.video
* image-storage.document
* image-storage.video_gallery
* image-storage.gallery
* image-storage.image
* image-storage.tag

## Особенности управление в VIS CMS
### Общее управление
Интерфейс максимально приближен к интерфейсу VIS-CMS. </br>
В шапке каждого из разделов находится инструментальная панель с фильтами(с некоторым уникальными для разделов) и кнопкой создания новой записи, которая вызовет модальное окно создания. </br>
В нижней части  страницы находится пагинация

### Общее управление для изображений, видео и документов
При клике на уже добавленный объект вызовется модальное окно редактирования записи </br>
Которое содержит вкладки размеров и поля указанные в соотв. конфиге. Возможно прямо управление связями конктретного изображения.</br></br>

При выделении области с объектами будет отображена панель множественного управления объектами.</br>
После выделения область возможно точечное добавление\удаление объектов с помощью нажатия на них мышкой при зажатой кнопке ctrl. </br>

#### Управление изображениями
При клике на поле "Загрузить изображение" появится окон выбора загружаемых изображений. Поддерживается множественная загрузка изображений. </br>
После загрузки всех изображений и созданиях их дополнительных размеров будет отправлен запрос на их оптимизацию.</br>
Если какое-либо из сгенирированных изображений-размеров не устаривает, тогда его можно заменить в вкладке этого размера.

#### Управление видео
При создании нового видео достаточно указать видео сервис и идентификатор видео. Если видео существует - оно будет добавлено в общий список. </br>
Поля title и description автоматически будут заполнены, если установлена соотв. настройка в конфиге. </br>
Если изображение-превью предоставляемое видео сервисом не устаривает, тогда можно загрузить собственное в вкладке "Превью".

#### Управление документами
При клике на поле "Загрузить документ" появится окон выбора загружаемых документов. Поддерживается множественная загрузка документов. </br>
После загрузки всех документов во все дополнительные поля файлов будет установлена ссылка на исходный файл. </br>
Если есть необходимость заменить файл для какого-либо из размеров, тогда его можно заменить в вкладке этого размера. 

### Общее управление для фотогалерей и видеогалерей
После создания галереи и добавления изображений\видео в неё возможно управление порядком изображений\видео путем перетягивания их между собой. </br>
Возможно установление одного из изображений\видео как превью для галереи с помощью нажатия на него мышкой при зажатой кнопке ctrl. 
