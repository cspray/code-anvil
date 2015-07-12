<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodIsPublic extends UnitTestConstraint {

    private $className;

    public function __construct(string $className) {
        parent::__construct();
        $this->className = $className;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasMethod($val)) {
            $m = $r->getMethod($val);
            return $m->isPublic();
        }

        return false;
    }

    public function toString() : string {
        return 'is a public method on ' . $this->className;
    }

}