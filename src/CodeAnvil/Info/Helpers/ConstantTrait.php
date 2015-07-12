<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

use CodeAnvil\Info\ConstantInfo;

trait ConstantTrait {

    private $constants = [];

    /**
     * @param ConstantInfo $constant
     * @return $this
     */
    public function addConstant(ConstantInfo $constant) : self {
        $this->constants[] = $constant;
        return $this;
    }

    /**
     * @return ConstantInfo[]
     */
    public function getConstants() : array {
        return $this->constants;
    }

}