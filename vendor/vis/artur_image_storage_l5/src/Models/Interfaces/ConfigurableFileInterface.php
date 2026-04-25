<?php namespace Vis\ImageStorage;

interface ConfigurableFileInterface
{
    public function getConfigUseSourceTitle();

    public function getConfigDeleteFiles();

    public function getConfigRenameFiles();

    public function getConfigSizeValidation();

    public function getConfigSizeMax();

    public function getConfigSizeMaxErrorMessage();

    public function getConfigExtensionValidation();

    public function getConfigAllowedExtensions();

    public function getConfigExtensionsErrorMessage();

    public function getConfigOptimization();

    public function getConfigQuality();

    public function getConfigStoreEXIF();

    public function getConfigSizes();

    public function getConfigSizesModifiable();

    public function getConfigSizeInfo($size);
}
