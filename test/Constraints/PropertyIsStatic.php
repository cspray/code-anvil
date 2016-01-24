<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class PropertyIsStatic extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = (string) $className;
    }

    public function matches($property) : bool {
        $r = new ReflectionClass($this->className);
        $prop = $r->getProperty($property);

        return $prop->isStatic();
    }

    public function toString() : string {
        return 'is a static property on ' . $this->className;
    }

}