<?php

declare(strict_types=1);

namespace CodeAnvil\Info\Helpers;

use CodeAnvil\Info\PropertyInfo;

trait PropertiesTrait {

    private $properties = [];

    /**
     * @param PropertyInfo $propertyInfo
     * @return $this
     */
    public function addProperty(PropertyInfo $propertyInfo) : self {
        $this->properties[] = $propertyInfo;
        return $this;
    }

    /**
     * @return PropertyInfo[]
     */
    public function getProperties() : array {
        return $this->properties;
    }

}