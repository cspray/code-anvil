<?php

namespace Cspray\CodeAnvil\Info\Helpers;

trait NameTrait {

    private $name;

    public function setName(string $name) : self {
        $this->name = $name;
        return $this;
    }

    public function getName() : string {
        return $this->name;
    }

}