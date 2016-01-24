<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class TypeIsinterface extends UnitTestConstraint {

    public function matches($type) : bool {
        return (new ReflectionClass(($type)))->isInterface();
    }

    public function toString() : string {
        return 'is an interface';
    }

}