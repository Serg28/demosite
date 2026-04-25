<?php

namespace App\Services\Checkouts\MonoPayParts\src\Validation;

trait SupportValidator
{
    private $validator = null;

    private $rule = null;

    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function setRule(Rule $rule)
    {
        $this->rule = $rule;

        return $this;
    }

    protected function validator(): Validator
    {
        if ($this->validator === null) {
            $this->validator = new Validator();
        }

        return $this->validator;
    }

    protected function rule(): Rule
    {
        if ($this->rule === null) {
            $this->rule = new Rule();
        }

        return $this->rule;
    }
}
