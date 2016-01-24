<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;

class PropertyHasDefaultValue extends UnitTestConstraint {

    private $class;
    private $propertyName;

    public function __construct(string $class, string $propertyName) {
        parent::__construct();
        $this->class = $class;
        $this->propertyName = $propertyName;
    }

    public function matches($val) : bool {
        $r = new ReflectionClass($this->class);
        $props = $r->getDefaultProperties();
        $prop = $this->propertyName;
        if (!array_key_exists($prop, $props)) {
            return false;
        }

        if ($val !== $props[$prop]) {
            return false;
        }

        return true;
    }

    public function toString() : string {
        return 'is assigned to ' . $this->class . '::' . $this->propertyName;
    }

}