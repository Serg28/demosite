<?php namespace Vis\ImageStorage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

abstract class AbstractImageStorageFile extends AbstractImageStorage implements ChangeableSchemeFileInterface, ConfigurableFileInterface, UploadableFileInterface
{
    use ChangeableSchemeFileTrait,
        ConfigurableFileTrait;

    protected $sizePrefix = 'file_';
    protected $prefixPath = '/storage/image-storage/';

    protected $sourceFile;

    protected static function boot()
    {
        parent::boot();

        static::updating(function (AbstractImageStorageFile $item) {
           $item->doRenameFiles();
        });

        static::deleted(function (AbstractImageStorageFile $item) {
            $item->doDeleteFiles();
        });
    }

    public function getSource($size = 'source')
    {
        return $this->file_folder . ($this->{$this->sizePrefix . $size} ?: $this->{$this->sizePrefix . 'source'});
    }

    protected function getPublicPath($size = 'source')
    {
        return public_path() . $this->getSource($size);
    }

    public function getFileExtension($size = 'source')
    {
        return pathinfo($this->getPublicPath($size), PATHINFO_EXTENSION);
    }

    public function getFileName($size = 'source')
    {
        return pathinfo($this->getPublicPath($size), PATHINFO_FILENAME);
    }

    public function getFileSize($size = 'source')
    {
        return filesize_format(filesize($this->getPublicPath($size)));
    }

    public function getFileMimeType($size = 'source')
    {
        return mime_content_type($this->getPublicPath($size));
    }

    private function validateFileSize()
    {
        if (!$this->getConfigSizeValidation()) {
            return true;
        }

        $uploadFileSize = $this->sourceFile->getSize();
        $maxFileSize = $this->getConfigSizeMax();

        if ($uploadFileSize > $maxFileSize) {
            $maxFileSizeInMB = $maxFileSize / 1000000;
            $message = $this->getConfigSizeMaxErrorMessage();
            $errorMessage = str_replace("[size]", $maxFileSizeInMB, $message);
            $this->setErrorMessage($errorMessage);
            return false;
        }

        return true;
    }

    private function validateFileExtension()
    {
        if (!$this->getConfigExtensionValidation()) {
            return true;
        }

        $uploadFileExtension = strtolower($this->sourceFile->getClientOriginalExtension());
        $allowedExtensions = $this->getConfigAllowedExtensions();

        if (!in_array($uploadFileExtension, $allowedExtensions)) {
            $allowedExtensionsList = implode(",", $allowedExtensions);
            $errorMessage = str_replace("[extension_list]", $allowedExtensionsList, $this->getConfigExtensionsErrorMessage());
            $this->setErrorMessage($errorMessage);
            return false;
        }

        return true;
    }

    public function setSourceFile(UploadedFile $file)
    {
        if (!$this->sourceFile = $file) {
            return false;
        }

        if (!$this->validateFileSize()) {
            return false;
        }

        if (!$this->validateFileExtension()) {
            return false;
        }

        return true;
    }

    private function setFileTitle()
    {
        if ($this->getConfigUseSourceTitle()) {
            $title = strstr($this->sourceFile->getClientOriginalName(), "." . $this->sourceFile->getClientOriginalExtension(), true);
        } else {
            $title = md5_file($this->sourceFile->getPathName()) . '_' . time();
        }

        $this->title = $title;
    }

    private function makeFolderPath()
    {
        return date('Y/m/d', strtotime($this->created_at)) . '/' . $this->id . '/';
    }

    private function setFileFolder()
    {
        $this->file_folder = $this->prefixPath . $this->makeFolderPath();
    }

    protected function makeFolders($size = 'source')
    {
        $prefixPath = $this->prefixPath;
        $postfixPath = $this->makeFolderPath();

        $chunks = explode("/", $postfixPath);
        $chunks[] = $size;

        $tempPath = public_path() . $prefixPath;

        foreach ($chunks as $chunk) {
            $tempPath = $tempPath . $chunk;

            if (!file_exists($tempPath)) {
                if (!mkdir($tempPath, 0755, true)) {
                    throw new Exception('Unable to create the directory [' . $tempPath . ']');
                }
            }
            $tempPath = $tempPath . '/';
        }

        $destinationPath = $prefixPath . $postfixPath . $size . '/';

        return $destinationPath;
    }

    protected function makeFileName()
    {
        return $this->getSlug() . "_" . time();
    }

    protected function makeExtension($size = 'source')
    {
        if ($this->sourceFile) {
           return strtolower($this->sourceFile->getClientOriginalExtension());
        }

       return $this->getFileExtension($size) ?: $this->getFileExtension();
    }

    public function makeFile($size = 'source')
    {
        if ($this->sourceFile) {
            $destinationPath = public_path() . $this->makeFolders($size);
            $fileName = $this->makeFileName() . "." . $this->makeExtension($size);

            $this->sourceFile->move($destinationPath, $fileName);
            $this->{$this->sizePrefix . $size} = $size . "/" . $fileName;
            $this->sourceFile = null;
        } else {
            $this->{$this->sizePrefix . $size} = $this->{$this->sizePrefix . "source"};
        }

        return true;
    }
    
    private function doSizesVariations()
    {
        $this->doCheckSchemeSizes();

        $sizes = $this->getConfigSizesModifiable();
        foreach ($sizes as $size => $info) {
            $this->makeFile($size);
        }
    }

    public function saveFile()
    {
        DB::beginTransaction();

        try {
            $this->setFileTitle();
            $this->save();

            $this->setFileFolder();
            $this->makeFile();
            $this->doSizesVariations();
            $this->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            return false;
        }
    }

    public function saveFileSize($size = 'source')
    {
        DB::beginTransaction();

        try {
            $this->makeFile($size);
            $this->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            return false;
        }
    }

    private function doDeleteFiles()
    {
        if (!$this->getConfigDeleteFiles()) {
            return false;
        }

        if (!$this->file_folder) {
            return false;
        }

        $fileFolder = public_path() . rtrim($this->file_folder, "/");

        if (public_path() == $fileFolder) {
            return false;
        }

        File::deleteDirectory($fileFolder);

        return true;
    }

    private function doRenameFiles()
    {
        if (!$this->getConfigRenameFiles()) {
            return false;
        }
        if (!$this->isDirty('title')) {
            return false;
        }

        $sizes = $this->getConfigSizes();
        foreach ($sizes as $size => $info) {
            $imagePath = $this->getPublicPath();
            $newName = $size . "/" . $this->makeFileName() . "." . $this->makeExtension($size);
            $newPath = public_path() . $this->file_folder . $newName;

            if (File::move($imagePath, $newPath)) {
                $this->{$this->sizePrefix . $size} = $newName;
            }
        }

        return true;
    }

}
