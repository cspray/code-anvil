<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info\Helpers;

trait FinalTrait {

    private $isFinal = false;

    public function makeFinal() : self {
        $this->isFinal = true;
        return $this;
    }

    public function isFinal() : bool {
        return $this->isFinal;
    }

}