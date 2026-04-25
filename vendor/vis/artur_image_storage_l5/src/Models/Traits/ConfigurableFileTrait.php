<?php namespace Vis\ImageStorage;

trait ConfigurableFileTrait
{
    public function getConfigUseSourceTitle()
    {
        return $this->getConfigValue('source_title');
    }

    public function getConfigDeleteFiles()
    {
        return $this->getConfigValue('delete_files');
    }

    public function getConfigRenameFiles()
    {
        return $this->getConfigValue('rename_files');
    }

    public function getConfigSizeValidation()
    {
        return $this->getConfigValue('size_validation.enabled');
    }

    public function getConfigSizeMax()
    {
        return $this->getConfigValue('size_validation.max_size');
    }

    public function getConfigSizeMaxErrorMessage()
    {
        return $this->getConfigValue('size_validation.error_message');
    }

    public function getConfigExtensionValidation()
    {
        return $this->getConfigValue('extension_validation.enabled');
    }

    public function getConfigAllowedExtensions()
    {
        return $this->getConfigValue('extension_validation.allowed_extensions');
    }

    public function getConfigExtensionsErrorMessage()
    {
        return $this->getConfigValue('extension_validation.error_message');
    }

    public function getConfigOptimization()
    {
        return $this->getConfigValue('optimization');
    }

    public function getConfigQuality()
    {
        return $this->getConfigValue('quality');
    }

    public function getConfigStoreEXIF()
    {
        return $this->getConfigValue('store_exif');
    }

    public function getConfigSizes()
    {
        return $this->getConfigValue('sizes');
    }

    public function getConfigSizesModifiable()
    {
        $allSizes = $this->getConfigSizes();
        unset($allSizes['source']);
        return $allSizes;
    }

    public function getConfigSizeInfo($size)
    {
        $allSizes = $this->getConfigSizes();
        return $allSizes[$size];
    }

}
