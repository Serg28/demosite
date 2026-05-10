<?php

namespace App\Cms\Definitions;

use App\Models\LegalEntitiesRecipient;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Froala;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Fields\Textarea;
use Linecore\Cms\Services\Actions;

class LegalEntitiesRecipients extends Resource
{
    public $model = LegalEntitiesRecipient::class;

    public $title = 'Юридичні особи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            'Основне' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Назва', 'title')->filter()->sortable()->language(),
                Froala::make('Реквізити', 'detail')->language(),
                Textarea::make('Реквізити в SMS', 'sms_detail')->onlyForm()->language(),
                Checkbox::make('За замовчуванням', 'is_default')->filter()->sortable(),
            ],
            'Дані Checkbox.ua' => [
                Text::make('Domain', 'checkbox_domain')->default(config('services.checkbox_ua.domain'))->onlyForm(),
                Text::make('Login', 'checkbox_login')->default(config('services.checkbox_ua.login'))->onlyForm(),
                Text::make('Password', 'checkbox_password')->default(config('services.checkbox_ua.password'))->onlyForm(),
                Text::make('License key', 'checkbox_license_key')->default(config('services.checkbox_ua.license_key'))->onlyForm(),
            ],
        ];
    }

    public function actions(): Actions
    {
        return Actions::make($this)->insert()->update()->delete();
    }
}
