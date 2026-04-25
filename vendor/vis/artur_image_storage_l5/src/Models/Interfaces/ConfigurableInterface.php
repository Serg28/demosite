<?php namespace Vis\ImageStorage;

interface ConfigurableInterface
{
    public function getConfigNamespace();

    public function getConfigPrefix();

    public function getConfigValue($value);

    public function getConfigTitle();

    public function getConfigPerPage();

    public function getConfigFields();

    public function getConfigFieldsNames();
}
