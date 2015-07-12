<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/ExampleRunner.php';

use CodeAnvil\Info\InterfaceInfo;

/**
 * @license See LICENSE file in project root
 */

$info = (new InterfaceInfo())->setNamespace('CodeAnvil\\Examples');

$interfaceName = ExampleRunner::getUserInput('Give us a name for the interface:');

$info->setName($interfaceName);

ExampleRunner::generateAndRequireCode($info);