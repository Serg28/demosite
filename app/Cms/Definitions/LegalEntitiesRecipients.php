<?php

namespace App\Cms\Definitions;

use App\Models\LegalEntitiesRecipient;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Actions;

class LegalEntitiesRecipients extends Resource
{
    public $model = LegalEntitiesRecipient::class;

    public $title = 'Юридичні особи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Название', 'title')->filter()->sortable()->language(),
                Froala::make('Реквизиты', 'detail')->sortable()->language(),
                Textarea::make('Реквизиты в SMS', 'sms_detail')->onlyForm(),
                Checkbox::make('По-умолчанию', 'is_default')->filter()->sortable(),
            ],
            'Данные Checkbox.ua' => [
                Text::make('Domain', 'checkbox_domain')->default(config('services.checkbox_ua.domain'))->onlyForm(),
                Text::make('Login', 'checkbox_login')->default(config('services.checkbox_ua.login'))->onlyForm(),
                Text::make('Password', 'checkbox_password')->default(config('services.checkbox_ua.password'))->onlyForm(),
                Text::make('License key', 'checkbox_license_key')->default(config('services.checkbox_ua.license_key'))->onlyForm(),
            ],
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete();
    }
}
