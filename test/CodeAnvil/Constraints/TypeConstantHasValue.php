<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeConstantHasValue extends UnitTestConstraint {

    private $className;
    private $constName;

    public function __construct(string $className, string $constName) {
        parent::__construct();
        $this->className = (string) $className;
        $this->constName = (string) $constName;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasConstant($this->constName)) {
            return $r->getConstant($this->constName) === $val;
        }

        return false;
    }

    public function toString() : string {
        return 'is the value for constant ' . $this->className . '::' . $this->constName;
    }

}