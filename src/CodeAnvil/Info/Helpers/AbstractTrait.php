<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

trait AbstractTrait {

    private $isAbstract = false;

    public function makeAbstract() : self {
        $this->isAbstract = true;
        return $this;
    }

    public function isAbstract() : bool {
        return $this->isAbstract;
    }

}