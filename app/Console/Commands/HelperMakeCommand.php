<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HelperMakeCommand extends Command
{
    protected $signature = 'make:helper {name}';

    protected $description = 'Create a new helper';

    public function handle()
    {
        $helperName = $this->argument('name');
        $dirPath = $helperPath = app_path('Helpers/');
        $helperPath = $dirPath . $helperName . '.php';
        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0777, true, true);
        }

        if (File::exists($helperPath)) {
            $this->error('Helper already exists!');
            return false;
        }

        File::put($helperPath, $this->helperTemplate($helperName));
        $this->info('Helper created successfully.');
    }

    protected function makeDirectory($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    protected function helperTemplate($helperName)
    {
        return "<?php\n\n" .
            "namespace App\Helpers;\n\n" .
            "class " . $helperName .
            "{\n" .
            "        // TODO: Add helper code here\n" .
            "}\n";
    }
}
