<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodParameterHasDefaultValue extends UnitTestConstraint {

    private $className;
    private $methodName;
    private $paramIndex;

    public function __construct(string $className, string $methodName, int $paramIndex) {
        parent::__construct();
        $this->className = $className;
        $this->methodName = $methodName;
        $this->paramIndex = $paramIndex;
    }

    public function matches($defaultValue) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasMethod($this->methodName)) {
            $m = $r->getMethod($this->methodName);
            $params = $m->getParameters();
            if (isset($params[$this->paramIndex])) {
                /** @var \ReflectionParameter $p */
                $p = $params[$this->paramIndex];
                return $p->isDefaultValueAvailable() && $p->getDefaultValue() === $defaultValue;
            }
        }
    }

    public function toString() : string {
        return 'is the default value for ' . $this->paramIndex . ' index parameter of ' . $this->className . '::' . $this->methodName;
    }

}