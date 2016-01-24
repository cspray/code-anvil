<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class TypeHasMethod extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($method) : bool {
        return (new ReflectionClass($this->className))->hasMethod($method);
    }

    public function toString() : string {
        return 'is a method on ' . $this->className;
    }

}