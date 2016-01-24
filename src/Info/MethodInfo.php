<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info;

class MethodInfo {

    use Helpers\DocCommentTrait;
    use Helpers\VisibilityTrait;
    use Helpers\FinalTrait;
    use Helpers\AbstractTrait;
    use Helpers\StaticTrait;
    use Helpers\NameTrait;

    private $parameters = [];
    private $body;
    private $returnType;

    public function addParameter(ParameterInfo $parameter) : self {
        $this->parameters[] = $parameter;
        return $this;
    }

    public function getParameters() : array {
        return $this->parameters;
    }

    public function setBody(string $body) : self {
        $this->body = $body;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setReturnType(string $returnType, string $alias = null) : self {
        $this->returnType = ['name' => $returnType, 'alias' => $alias];
        return $this;
    }

    public function getReturnType() : array {
        return $this->returnType;
    }

    public function hasReturnType() : bool {
        return !empty($this->returnType);
    }

}