<?php

namespace App\Cms\Fields;

use Illuminate\Support\Str;
use Vis\Builder\Fields\Field;

class Tinymce extends Field
{
    private string $toolbar = "undo redo | bold italic underline subscript superscript removeformat | alignleft aligncenter alignright | indent outdent | bullist numlist | link unlink anchor | code | preview fullscreen | table | image responsivefilemanager";

    private string $options = '';

    private string $plugins = "anchor autolink lists spellchecker pagebreak layer table save hr modxlink image imagetools emoticons insertdatetime preview media searchreplace print code contextmenu paste directionality fullscreen noneditable visualchars visualblocks textcolor nonbreaking template youtube autosave advlist visualblocks charmap wordcount codesample colorpicker responsivefilemanager";

    private array $templates = [
        [
            'title' => 'Пример шаблона',
            'description' => 'Some desc 1',
            'content' => 'My content'
        ]
    ]; // [ ['title' => 'Some title 1', 'description' => 'Some desc 1', 'content' => 'My content'], [..,]]
    //https://www.tiny.cloud/docs/tinymce/latest/template/

    public function toolbar($value)
    {
        $this->toolbar = $value;

        return $this;
    }

    public function plugins($value)
    {
        $this->plugins = $value;

        return $this;
    }

    public function options($collection)
    {
        $this->options = $collection;

        return $this;
    }

    public function templates(array $value)
    {
        $this->templates = $value;

        return $this;
    }

    public function getTemplates()
    {
        return json_encode($this->templates);
    }

    public function getToolbar()
    {
        return $this->toolbar;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function getOptions()
    {
        return json_encode($this->options);
    }

    public function getValueForList($definition)
    {
        $arrayValue = json_decode($this->getValue());

        $value = $arrayValue->{$this->locale} ?? $this->getValue();

        return Str::limit(strip_tags($value), 70);
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        $thisLang = request()->cookie('lang_admin') ?: array_key_first(config('builder.translations.cms.languages'));

        return view('cms.fields.' . $nameField, compact('definition', 'field', 'thisLang'))->render();
    }
}
