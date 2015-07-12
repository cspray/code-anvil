<?php

declare(strict_types=1);

namespace CodeAnvil\Info;

class InterfaceInfo implements Info {

    use Helpers\DeclareStrictTrait;
    use Helpers\DocCommentTrait;
    use Helpers\NamespaceTrait;
    use Helpers\NameTrait;
    use Helpers\ConstantTrait;
    use Helpers\MethodsTrait;

    private $interfaces = [];

    public function addExtendedInterface(string $interface, string $alias = null) : self {
        $this->interfaces[] = [
            'name' => $interface,
            'alias' => $alias
        ];
        return $this;
    }

    public function getExtendedInterfaces() : array {
        return $this->interfaces;
    }

}