<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

trait StaticTrait {

    private $isStatic = false;

    public function makeStatic() : self {
        $this->isStatic = true;
        return $this;
    }

    public function isStatic() : bool {
        return $this->isStatic;
    }

}