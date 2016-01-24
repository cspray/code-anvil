<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Test\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;
use ReflectionClass;
use ReflectionMethod;

class MethodHasReturnType extends UnitTestConstraint {

    private $className;
    private $methodName;

    public function __construct(string $className, string $methodName) {
        parent::__construct();
        $this->className = $className;
        $this->methodName = $methodName;
    }

    public function matches($type) : bool {
        $r = new ReflectionClass($this->className);
        if ($r->hasMethod($this->methodName)) {
            return $this->hasReturnType($r->getMethod($this->methodName));
        }

        return false;
    }

    private function hasReturnType(ReflectionMethod $method) : bool {
        $v = explode("\n", (string) $method);
        array_pop($v);
        array_pop($v);
        $r = trim(array_pop($v));
        if (substr($r, 0, 8) === '- Return') {
            return true;
        }
        return false;
    }

    public function toString() : string {
        return 'is a return type declaration on ' . $this->className . '::' . $this->methodName;
    }

}