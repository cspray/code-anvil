<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/ExampleRunner.php';

use CodeAnvil\Info\ConstantInfo;
use CodeAnvil\Info\InterfaceInfo;

/**
 * @license See LICENSE file in project root
 */

$foo = (new ConstantInfo())->setName('FOO')->setDefaultValue(true);
$bar = (new ConstantInfo())->setName('BAR')->setDefaultValue(['foo', 'bar']);
$baz = (new ConstantInfo())->setName('BAZ')->setDefaultValue(1.1);

$interface = (new InterfaceInfo())->setNamespace('CodeAnil\\Examples')->setName('InterfaceWithConstants');
$interface->addConstant($foo)->addConstant($bar)->addConstant($baz);

ExampleRunner::generateAndRequireCode($interface);
