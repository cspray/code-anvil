<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeHasNamespace extends UnitTestConstraint {

    private $ns;

    public function __construct(string $ns) {
        parent::__construct();
        $this->ns = $ns;
    }

    public function matches($value) : bool {
        $r = new \ReflectionClass($value);
        return strtolower($this->ns) === strtolower($r->getNamespaceName());
    }

    public function toString() : string {
        return 'has a namespace of ' . $this->ns;
    }

}