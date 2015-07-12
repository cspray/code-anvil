<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodParameterIsRequired extends UnitTestConstraint {

    private $className;
    private $methodName;

    public function __construct(string $className, string $methodName) {
        parent::__construct();
        $this->className = $className;
        $this->methodName = $methodName;
    }

    public function matches($paramIndex) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasMethod($this->methodName)) {
            $m = $r->getMethod($this->methodName);
            $params = $m->getParameters();
            if (isset($params[$paramIndex])) {
                $p = $params[$paramIndex];
                return !$p->isDefaultValueAvailable();
            }
        }

        return false;
    }

    public function toString() : string {
        return 'index prameter for ' . $this->className . '::' . $this->methodName . ' is required';
    }

}