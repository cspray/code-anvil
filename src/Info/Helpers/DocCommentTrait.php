<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info\Helpers;

trait DocCommentTrait {

    private $docComment;

    public function setDocComment(string $docComment) : self {
        $this->docComment = $docComment;
        return $this;
    }

    public function getDocComment() {
        return $this->docComment;
    }

    public function hasDocComment() {
        return !empty($this->docComment);
    }

}