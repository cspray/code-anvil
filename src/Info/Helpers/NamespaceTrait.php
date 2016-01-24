<?php

declare(strict_types = 1);

namespace Cspray\CodeAnvil\Info\Helpers;

trait NamespaceTrait {

    private $namespace;

    public function setNamespace(string $namespace) : self {
        $this->namespace = $namespace;
        return $this;
    }

    public function getNamespace() {
        return $this->namespace;
    }

}