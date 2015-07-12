<?php

declare(strict_types=1);

namespace CodeAnvil\Info;

class TraitInfo implements Info {

    use Helpers\DeclareStrictTrait;
    use Helpers\DocCommentTrait;
    use Helpers\NamespaceTrait;
    use Helpers\NameTrait;
    use Helpers\PropertiesTrait;
    use Helpers\MethodsTrait;

}