<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeIsinterface extends UnitTestConstraint {

    public function matches($type) : bool {
        return (new \ReflectionClass(($type)))->isInterface();
    }

    public function toString() : string {
        return 'is an interface';
    }

}