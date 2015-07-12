<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeHasMethod extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($method) : bool {
        return (new \ReflectionClass($this->className))->hasMethod($method);
    }

    public function toString() : string {
        return 'is a method on ' . $this->className;
    }

}