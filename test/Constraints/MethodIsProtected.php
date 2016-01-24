<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class MethodIsProtected extends UnitTestConstraint {

    private $className;

    public function __construct(string $type) {
        parent::__construct();
        $this->className = $type;
    }

    public function matches($val) : bool {
        $r = new ReflectionClass($this->className);
        if ($r->hasMethod($val)) {
            $m = $r->getMethod($val);
            return $m->isProtected();
        }

        return false;
    }

    public function toString() : string {
        return 'is a protected method on ' . $this->className;
    }

}