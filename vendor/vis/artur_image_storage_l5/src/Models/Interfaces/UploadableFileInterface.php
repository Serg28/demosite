<?php namespace Vis\ImageStorage;

use Illuminate\Http\UploadedFile;

interface UploadableFileInterface
{
    public function setSourceFile(UploadedFile $file);

    public function makeFile($size = 'source');

    public function saveFile();

    public function saveFileSize($size = 'source');

}
