<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

trait DefaultValueTrait {

    private $defaultValue;
    private $defaultValueSet = false;

    public function setDefaultValue($value) : self {
        $this->defaultValueSet = true;
        $this->defaultValue = $value;
        return $this;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

    public function hasDefaultValue() : bool {
        return $this->defaultValueSet;
    }

}