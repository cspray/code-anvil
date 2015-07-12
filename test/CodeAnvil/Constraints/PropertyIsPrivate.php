<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class PropertyIsPrivate extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = (string) $className;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->className);
        $p = $r->getProperty($val);

        return $p->isPrivate();
    }

    public function toString() : string {
        return 'is not a private property of ' . $this->className;
    }


}