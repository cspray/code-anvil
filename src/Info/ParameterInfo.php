<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info;

class ParameterInfo {

    use Helpers\NameTrait;
    use Helpers\DefaultValueTrait;

    private $byReference = false;
    private $typeDeclaration;
    private $isVariadic = false;

    public function setTypeDeclaration(string $type, string $alias = null) : self {
        $this->typeDeclaration = ['name' => $type, 'alias' => $alias];
        return $this;
    }

    public function getTypeDeclaration() {
        return $this->typeDeclaration;
    }

    public function hasTypeDeclaration() {
        return !empty($this->typeDeclaration);
    }

    public function makeVariadic() : self {
        $this->isVariadic = true;
        return $this;
    }

    public function isVariadic() : bool {
        return $this->isVariadic;
    }

    public function makeByReference() : self {
        $this->byReference = true;
        return $this;
    }

    public function isByReference() : bool {
        return $this->byReference;
    }
}