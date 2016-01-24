<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class PropertyIsPrivate extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = (string) $className;
    }

    public function matches($val) : bool {
        $r = new ReflectionClass($this->className);
        $p = $r->getProperty($val);

        return $p->isPrivate();
    }

    public function toString() : string {
        return 'is not a private property of ' . $this->className;
    }


}