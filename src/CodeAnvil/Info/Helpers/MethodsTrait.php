<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

use CodeAnvil\Info\MethodInfo;

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