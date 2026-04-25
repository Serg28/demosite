<?php

namespace App\Cms\Definitions;

use App\Models\FilesForWebmaster as ModelFile;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\File;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class FilesForWebmaster extends Resource
{
    public $model = ModelFile::class;

    public $title = 'Загрузка файлов';

    protected $orderBy = 'id asc';

    protected $isSortable = false;

    public function fields(): array
    {
        return [
            Text::make('Название', 'title')->filter()->sortable(),
            File::make('Файл', 'file')
                ->uploadPath('/')
                ->accept('.html, .txt')
                ->noFileSelection(),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete();
    }
}
