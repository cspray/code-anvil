<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeImplementsInterface extends UnitTestConstraint {

    private $interface;

    public function __construct(string $interface) {
        parent::__construct();
        $this->interface = $interface;
    }

    public function matches($className) : bool {
        return in_array($this->interface, (new \ReflectionClass($className))->getInterfaceNames());
    }

    public function toString() : string {
        return 'implements ' . $this->interface;
    }

}