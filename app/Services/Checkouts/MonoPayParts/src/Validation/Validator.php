<?php

namespace App\Services\Checkouts\MonoPayParts\src\Validation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation;
use Illuminate\Validation;

class Validator
{
    private $factory = null;

    public function __construct(string $namespace = 'lang', string $group = 'validation', string $locale = 'en')
    {
        $this->factory = new Validation\Factory($this->loadTranslator($namespace, $group, $locale));
    }

    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->factory, $method],
            $args
        );
    }

    /**
     * Validate the given data against the provided rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->factory->validate($data, $rules, $messages, $customAttributes);
    }

    protected function loadTranslator(string $namespace, string $group, string $locale)
    {
        $filesystem = new Filesystem();
        $loader = new Translation\FileLoader($filesystem, dirname(dirname(__FILE__)).'/'.$namespace);
        $loader->addNamespace(
            $namespace,
            dirname(dirname(__FILE__)).'/'.$namespace
        );
        $loader->load($locale, $group, $namespace);

        return new Translation\Translator($loader, $locale);
    }
}
