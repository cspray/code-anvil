<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class PropertyIsProtected extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->className);
        $p = $r->getProperty($val);

        return $p->isProtected();
    }

    public function toString() : string {
        return 'is not a protected property of ' . $this->className;
    }

}