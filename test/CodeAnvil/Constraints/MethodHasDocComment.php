<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class MethodHasDocComment extends UnitTestConstraint {

    private $type;
    private $method;

    public function __construct(string $type, string $method) {
        parent::__construct();
        $this->type = $type;
        $this->method = $method;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->type);
        if ($r->hasMethod($this->method)) {
            $m = $r->getMethod($this->method);
            return $m->getDocComment() === $val;
        }
    }

    public function toString() : string {
        return 'is a doc comment on ' . $this->type . '::' . $this->method . '()';
    }

}