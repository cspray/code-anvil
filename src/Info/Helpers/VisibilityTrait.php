<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil\Info\Helpers;

trait VisibilityTrait {

    private $visibility = 'public';

    public function setVisibility($visibility) : self {
        $this->visibility = (string) $visibility;
        return $this;
    }

    public function getVisibility() : string {
        return $this->visibility;
    }

}