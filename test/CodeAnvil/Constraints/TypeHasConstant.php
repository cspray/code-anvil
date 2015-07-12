<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeHasConstant extends UnitTestConstraint {

    private $expectedConstant;

    public function __construct(string $constant) {
        parent::__construct();
        $this->expectedConstant = $constant;
    }

    public function matches($value) : bool {
        return (new \ReflectionClass($value))->hasConstant($this->expectedConstant);
    }

    public function toString() : string {
        $str = var_export($this->expectedConstant, true);
        return "has specified constant:\n$str";
    }

}