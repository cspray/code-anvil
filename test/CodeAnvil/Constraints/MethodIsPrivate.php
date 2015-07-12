<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodIsPrivate extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasMethod($val)) {
            $m = $r->getMethod($val);
            return $m->isPrivate();
        }

        return false;
    }

    public function toString() : string {
        return 'is a private method on ' . $this->className;
    }

}