<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class MethodIsFinal extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = (string) $className;
    }

    public function matches($method) : bool {
        $r = new ReflectionClass($this->className);
        if ($r->hasMethod($method)) {
            return $r->getMethod($method)->isFinal();
        }

        return false;
    }

    public function toString() : string {
        return 'is a final method on ' . $this->className;
    }

}