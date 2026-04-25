<?php namespace Vis\ImageStorage;

trait ConfigurableTrait
{
    protected $configNamespace = "image-storage";
    protected $configPrefix;

    public function getConfigNamespace()
    {
        return $this->configNamespace;
    }

    public function getConfigPrefix()
    {
        return $this->configPrefix;
    }

    public function getConfigValue($value)
    {
        return config($this->getConfigNamespace() . '.' . $this->getConfigPrefix() . '.' . $value);
    }

    public function getConfigTitle()
    {
        return $this->getConfigValue('title');
    }

    public function getConfigPerPage()
    {
        return $this->getConfigValue('per_page');
    }

    public function getConfigFields()
    {
        return $this->getConfigValue('fields');
    }

    public function getConfigFieldsNames()
    {
        $columnNames = [];

        $configFields = $this->getConfigFields();

        foreach ($configFields as $field => $fieldInfo) {
            if (isset($fieldInfo['tabs'])) {
                foreach ($fieldInfo['tabs'] as $tab => $tabInfo) {
                    $columnNames[] = $field . $tabInfo['postfix'];
                }
            } else {
                $columnNames[] = $field;
            }
        }

        return $columnNames;
    }

}
