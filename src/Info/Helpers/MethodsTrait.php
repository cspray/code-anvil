<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info\Helpers;

use Cspray\CodeAnvil\Info\MethodInfo;

trait MethodsTrait {

    private $methods = [];

    public function addMethod(MethodInfo $methodInfo) : self {
        $this->methods[] = $methodInfo;
        return $this;
    }

    /**
     * @return MethodInfo[]
     */
    public function getMethods() : array {
        return $this->methods;
    }

}