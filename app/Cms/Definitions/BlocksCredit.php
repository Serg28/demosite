<?php

namespace App\Cms\Definitions;


use App\Cms\Definitions\HasManyBlocks\BlockAdvantages;
use App\Cms\Fields\ManyToManyAjaxProducts;
use App\Cms\Fields\ProductsCodes;
use App\Models\HasMany\Block;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\SelectWithPicture;
use Vis\Builder\Fields\Text;

class BlocksCredit extends Resource
{
    public $model = Block::class;

    public $title = 'Блоки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('model_id', 'model_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            SelectWithPicture::make('Блок', 'template')
                ->options([
                    '' => [
                        'value' => 'Выбрать блок',
                    ],
                    'form_credit_bank_monobank' => [
                        'value' => 'Калькулятор на стр (Monobank)',
                        'data-img' => glide('/blocks/form_credit_bank_monobank', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_monobank'
                    ],
                    'form_credit_bank_privatbank' => [
                        'value' => 'Калькулятор на стр (Privat Bank)',
                        'data-img' => glide('/blocks/form_credit_bank_privatbank', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_privatbank'
                    ],
                    'form_credit_bank_sensebank' => [
                        'value' => 'Калькулятор на стр (Sense Bank)',
                        'data-img' => glide('/blocks/form_credit_bank_sensebank', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_sensebank'
                    ],
                    'form_credit_bank_otpbank' => [
                        'value' => 'Калькулятор на стр (OTP Bank)',
                        'data-img' => glide('/blocks/form_credit_bank_otpbank', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_otpbank'
                    ],
                    'form_credit_bank_oldpay' => [
                        'value' => 'Калькулятор на стр (Плати Пизнише)',
                        'data-img' => glide('/blocks/form_credit_bank_oldpay', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_oldpay'
                    ],
                    'form_credit_bank_ideabank' => [
                        'value' => 'Калькулятор на стр (Idea Bank)',
                        'data-img' => glide('/blocks/form_credit_bank_ideabank', ['w' => 400, 'h' => 400]),
                        'data-class' => 'form_credit_bank_ideabank'
                    ],
                    'block_advantages_page_credit' => [
                        'value' => 'Блок описания',
                        'data-img' => glide('/blocks/block_advantages_page_credit', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_advantages_page_credit'
                    ],

                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_advantages_page_credit')
                ->hasOne('h2')
                ->onlyForm(),

            Froala::make('Малое Описание', 'short_description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_advantages_page_credit')
                ->hasOne('short_description')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_advantages_page_credit')
                ->hasOne('description')
                ->onlyForm(),

            Checkbox::make('Отображать', 'is_active')
                ->className('form_credit_bank_monobank form_credit_bank_privatbank form_credit_bank_sensebank form_credit_bank_otpbank form_credit_bank_oldpay form_credit_bank_ideabank block_advantages_page_credit')
                ->default(1),
//
//            Definition::make('Преимущества')
//                ->className('')
//                ->hasMany('advantages', BlockAdvantages::class)
//                ->className('advantages'),


        ];
    }
}
