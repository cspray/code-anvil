<?php

declare(strict_types=1);

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodParameterHasTypeDeclaration extends UnitTestConstraint {

    private $className;
    private $methodName;
    private $paramIndex;

    public function __construct(string $className, string $methodName, int $paramIndex) {
        parent::__construct();
        $this->className = $className;
        $this->methodName = $methodName;
        $this->paramIndex = $paramIndex;
    }

    public function matches($type) : bool {
        $r = new \ReflectionClass($this->className);
        if ($r->hasMethod($this->methodName)) {
            $m = $r->getMethod($this->methodName);
            $params = $m->getParameters();
            if (isset($params[$this->paramIndex])) {
                $p = $params[$this->paramIndex];
                if ($this->hasType($p)) {
                    return explode(' ', (string) $p)[4] === $type;
                }
            }

        }
    }

    private function hasType(\ReflectionParameter $parameter) : bool {
        $t = substr(explode(' ', (string) $parameter)[4], 0, 1);
        if ($t !== '$' && $t !== '&' && $t !== '.') { // handle variables, by-ref, and variadics
            return true;
        }
        return false;
    }

    public function toString() : string {
        return 'is the type declaration for ' . $this->paramIndex . ' index parameter on ' . $this->className . '::' . $this->methodName;
    }


}