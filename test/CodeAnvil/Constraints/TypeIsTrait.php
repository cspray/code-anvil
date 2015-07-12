<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeIsTrait extends UnitTestConstraint {

    public function matches($val) : bool {
        return (new \ReflectionClass($val))->isTrait();
    }

    public function toString() : string {
        return 'is a trait';
    }

}