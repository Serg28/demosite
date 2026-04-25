<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ConvertImagesToWebP extends Command
{
    protected $signature = 'images:convert-webp';

    protected $description = 'Convert images to WebP format';

    public function handle()
    {
        $directories = ['public/img']; // Замените путями к вашим папкам

        foreach ($directories as $directory) {
            $files = File::files($directory);

            foreach ($files as $file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = pathinfo($file, PATHINFO_FILENAME);

                if ($extension !== 'webp' && $extension !== 'svg') {
                    $webpPath = $directory . '/' . $filename . '.webp';

                    if (!File::exists($webpPath)) {
                        $image = Image::make($file);
                        $image->save($webpPath, 80);
                        $this->info("Converted: {$webpPath}");
                    } else {
                        $this->info("Skipped: {$webpPath}");
                    }
                }
            }
        }

        $this->info('Image conversion completed.');
    }
}
