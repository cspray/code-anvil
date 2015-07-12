<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class ClassIsFinal extends UnitTestConstraint {

    public function matches($value) : bool {
        return (new \ReflectionClass($value))->isFinal();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString() : string {
        return 'is a class that is marked final';
    }

}