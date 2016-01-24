<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class MethodIsStatic extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($method) : bool {
        $r = new ReflectionClass($this->className);
        if ($r->hasMethod($method)) {
            $m = $r->getMethod($method);
            return $m->isStatic();
        }

        return false;
    }

    public function toString() : string {
        return 'is a static method on ' . $this->className;
    }

}