<?php

namespace App\Cms\Definitions;

use App\Cms\Exports\ExportProductOptions;
use App\Cms\Exports\ExportProducts;
use App\Cms\Fields\ForeignTreeCategory;
use App\Cms\Fields\FroalaFixJson;

use App\Cms\Fields\Price;
use App\Cms\Fields\ManyToManyAjaxProducts;
use App\Cms\Fields\ProductsCodes;

use App\Cms\Fields\TextExt;
use App\Cms\Fields\Tinymce;
use App\Cms\Imports\ImportProductOptions;
use App\Cms\Imports\ImportProducts;
use App\Models\MorphOne\Seo;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\MultiImage;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\ReadonlyField;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Products extends Resource
{
    public $model = Product::class;

    public $title = 'Товары';

    protected $orderBy = 'id desc';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable()->filter(),
                Text::make('Заголовок', 'title')->language()->filter()->sortable()->transliteration('slug', true)->className('col-md-12'),

                Text::make('Slug', 'slug')
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('products')->ignore(request('id')),
                    ])->onlyForm()->comment('Url по-умолчанию. Если для какого-то языка заполнен url из поля ниже, то в данном языке товар доступен только по этому url, а поле slug игнорируется.'),

                TextExt::make('Url', 'url')
                    ->language()->autoTranslate(false)
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('products')->ignore(request('id')),
                    ])->onlyForm()->comment('Мультиязычный url. Имеет бОльший приоритет по сравнению с полем slug. Если для какого-то языка он заполнен, то в данном языке товар доступен только по этому url, а поле slug игнорируется.'),

                Text::make('Артикул', 'code')->filter()->sortable()->className('col-md-6')->readonlyForEdit(),
                Text::make('Код 1С', 'id_1c')->onlyForm()->className('col-md-6')->readonlyForEdit(),

                ForeignTreeCategory::make('Категория', 'category_id')
                    ->options((new Options('category'))->isJson())->rules([
                        'required',
                    ])
                    ->sortable()->filter(),

                ManyToMany::make('Дополнительные категории')->options(
                    (new Options('categories'))->keyField('title')
                )->onlyForm(),

                Price::make(setting('konvertirovat-cenu') ? 'Цена базовая' : 'Цена', 'price')->filter()->sortable()->className('col-md-4'),

                Price::make('Старая цена', 'price_old')->onlyForm()->className('col-md-4'),

                Number::make('Остаток', 'quantity')->filter()->sortable()->className('col-md-4'),

                Checkbox::make('Отображать', 'is_active')->filter()->sortable()->fastEdit(),

                Foreign::make('Статус', 'product_status_id')
                    ->filter()->sortable()
                    ->options((new Options('status'))->isJson())
                    ->fastEdit()->className('col-md-6'),

                Select::make('Статус отправки', 'delivery_status')
                    ->options([
                        'ready' => 'Готов к отправке',
                        'expected2-3' => 'Поступление 2-3 дня',
                    ])->onlyForm()->className('col-md-6'),


                Checkbox::make('Наличие гарантии', 'guarantee')->onlyForm()->className('col-md-12'),

                Number::make('Срок гарантии, мес.', 'guarantee_period')->onlyForm()->className('col-md-6'),

                Select::make('Тип гарантии', 'guarantee_type')
                    ->options([
                        'manufacturer' => 'От производителя',
                        'shop' => 'От магазина',
                    ])->onlyForm()->className('col-md-6'),
            ],

            'Контент' => [
                Froala::make('Описание', 'description')->language()->onlyForm(),
            ],

            'Фото и видео' => [
                Image::make('Фото', 'picture'),
                MultiImage::make('Дополнительные изображение', 'other_pictures')->onlyForm(),
                Text::make(
                    'Ссылка на видео (например https://www.youtube.com/watch?v=48f5a_v0WnQ)',
                    'link_to_youtube'
                )->onlyForm(),
            ],

            'Характеристики' => [
                Text::make('Код для связанных товаров', 'related_code')->onlyForm(),

                Definition::make('Характеристики')
                    ->hasMany('characteristics', ProductCharacteristicOptions::class),
            ],

            'Метки и связи' => [
                ManyToMany::make('Метки')
                    ->options(
                        (new Options('labels'))
                    ),

                ManyToManyAjaxProducts::make('Похожие товары')
                    ->options(
                        (new Options('interestingProducts'))->isJson()
                    ),
                Checkbox::make('На главную', 'to_main')->onlyForm(),

                Checkbox::make('Доступна рассрочка МОНО', 'has_mono_payparts')->onlyForm()->className('col-md-6'),
                Checkbox::make('Доступна оплата Приват ОЧ', 'has_privat_payparts')->onlyForm()->className('col-md-6'),
                Number::make('Макс. кол-во платежей МОНО', 'mono_payparts_count')->onlyForm()->className('col-md-6'),
                Number::make('Макс. кол-во платежей Приват ОЧ', 'privat_payparts_count')->onlyForm()->className('col-md-6'),
            ],

            'Комментарии' => [
                Definition::make('Комментарии')
                    ->hasMany('comments', CommentsForProduct::class),
            ],

            /* 'Связаные товары' => [

                 Text::make('Название для группы', 'name_group')->onlyForm()->language(),

                 Definition::make('Блоки')
                     ->hasMany('product_join_blocks', ProductJoinBlocks::class),
             ],*/

            'SEO' => Seo::fieldsForDefinitions(),

            'Статистика' => [
                ReadonlyField::make('Кол. просмотров', 'count_views')->onlyForm()->className('col-md-4'),
                ReadonlyField::make('Дата создания', 'created_at')->onlyForm()->default(Carbon::now())->className('col-md-4'),
                ReadonlyField::make('Дата обновления', 'updated_at')->onlyForm()->default(Carbon::now())->className('col-md-4'),
            ],
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->revisions()->delete();
    }

    public function buttons()
    {
        return [
            ExportProducts::class,
            //ImportProducts::class,
            //ExportProductOptions::class,
            //ImportProductOptions::class,
        ];
    }
}
