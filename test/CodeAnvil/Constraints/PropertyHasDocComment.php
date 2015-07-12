<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class PropertyHasDocComment extends UnitTestConstraint {

    private $type;
    private $property;

    public function __construct(string $type, string $property) {
        parent::__construct();
        $this->type = $type;
        $this->property = $property;
    }

    public function matches($val) : bool {
        $r = new \ReflectionClass($this->type);
        if ($r->hasProperty($this->property)) {
            $p = $r->getProperty($this->property);
            return $val === $p->getDocComment();
        }

        return false;
    }

    public function toString() : string {
        return 'is a doc comment for ' . $this->type . '::' . $this->property;
    }

}