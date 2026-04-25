<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Vis\Builder\Libs\GoogleTranslateForFree;

class Translate extends Command
{
    protected $signature = 'translate:text';

    protected $description = 'Auto translate script';

    private string $currentLang;

    private array $modelsForTranslation = [
        Product::class,
        Category::class,
        CharacteristicOption::class,
        Characteristic::class,
    ];

    private array $columns = ['title', 'description', 'short_description'];

    public function __construct()
    {
        $this->currentLang = App::getLocale();

        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('start auto translate');

        $this->executeTranslation();
        Artisan::call('cache:clear');

        $this->info('translating is done!');
    }

    private function executeTranslation(): void
    {
        foreach ($this->modelsForTranslation as $model) {
            $this->info('model - '.class_basename($model));
            $this->updateTable($this->getAllRecords($model));
        }
    }

    private function updateTable($records): void
    {
        foreach ($records as $record) {
            $record->update($this->getTranslateRecord($record));
            $this->info('translate and update');
        }
    }

    private function getTranslateRecord($record): array
    {
        $data = [];
        foreach ($this->columns as $name) {
            if ($record->$name) {
                $data[$name] = $this->translate($record->$name);
            }
        }

        return $data;
    }

    private function getAllRecords($model)
    {
        return $model::orderBy('id')->get();
    }

    private function translate(string $jsonText)
    {
        if ($jsonText && $jsonText != 'null') {
            $arrayText = json_decode($jsonText);
            $result = [];

            foreach ($arrayText as $lang => $text) {
                if ($text) {
                    $result[$lang] = $text;
                } else {
                    try {
                        $phraseTranslate = GoogleTranslateForFree::translate($this->currentLang, $lang, $arrayText->{$this->currentLang});
                    } catch (\Exception $e) {
                        $phraseTranslate = $arrayText->{$this->currentLang};
                    }

                    $result[$lang] = $phraseTranslate;

                    Log::info($result);
                    sleep(15);
                }
            }

            return json_encode($result);
        }
    }
}
