<?php

declare(strict_types=1);

/**
 * @license See LICENSE file in project root
 */

namespace CodeAnvil\Constraints;

use PHPUnit_Framework_Constraint as UnitTestConstraint;

class TypeHasDocComment extends UnitTestConstraint {

    private $type;

    public function __construct(string $type) {
        parent::__construct();
        $this->type = $type;
    }

    public function matches($expectDocComment) : bool {
        $r = new \ReflectionClass($this->type);
        return $expectDocComment === $r->getDocComment();
    }

    public function toString() : string {
        return 'is a doc comment on ' . $this->type;
    }

}