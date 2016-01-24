<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\CodeAnvil\Info\Helpers;


trait DeclareStrictTrait {

    private $declaredStrict = false;

    public function declareStrict() : self {
        $this->declaredStrict = true;
        return $this;
    }

    public function isDeclaredStrict() {
        return $this->declaredStrict;
    }

}