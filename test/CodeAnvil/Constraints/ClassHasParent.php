<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class ClassHasParent extends UnitTestConstraint {

    private $parentClass;

    public function __construct(string $parentClass) {
        parent::__construct();
        $this->parentClass = $parentClass;
    }

    public function matches($value) : bool {
        $r = new \ReflectionClass($value);

        return strtolower($this->parentClass) === strtolower($r->getParentClass()->getName());
    }

    public function toString() : string {
        return 'extends ' . $this->parentClass;
    }

}