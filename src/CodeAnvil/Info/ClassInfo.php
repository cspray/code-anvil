<?php

declare(strict_types=1);

namespace CodeAnvil\Info;

class ClassInfo implements Info {

    use Helpers\DeclareStrictTrait;
    use Helpers\DocCommentTrait;
    use Helpers\NamespaceTrait;
    use Helpers\AbstractTrait;
    use Helpers\FinalTrait;
    use Helpers\NameTrait;
    use Helpers\ConstantTrait;
    use Helpers\PropertiesTrait;
    use Helpers\MethodsTrait;

    private $parentClass;
    private $parentClassAlias;
    private $interfaces = [];
    private $traits = [];

    public function addImplementedInterface(string $fullyQualifiedInterface, string $alias = null) : self {
        $this->interfaces[] = [
            'name' => $fullyQualifiedInterface,
            'alias' => $alias
        ];
        return $this;
    }

    public function getImplementedInterfaces() : array {
        return $this->interfaces;
    }

    public function setParentClass(string $fullyQualifiedClassname, string $alias = null) : self {
        $this->parentClass = $fullyQualifiedClassname;
        $this->parentClassAlias = $alias;
        return $this;
    }

    public function getParentClass() {
        return $this->parentClass;
    }

    public function getParentClassAlias() {
        return $this->parentClassAlias;
    }

    public function addTrait(string $fullyQualifiedName, string $alias = null) : self {
        $this->traits[] = [
            'name' => $fullyQualifiedName,
            'alias' => $alias
        ];
        return $this;
    }

    public function getTraits() {
        return $this->traits;
    }

    public function hasTraits() {
        return !empty($this->traits);
    }

}