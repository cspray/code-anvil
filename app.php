<?php

require_once __DIR__ . '/vendor/autoload.php';

$classInfo = new \DataForge\Info\ClassInfo();

$classInfo->setNamespace('Foo\\Bar')
          ->setName('Bar');

$constructor = new \DataForge\Info\MethodInfo();
$constructor->setName('__construct');

$classInfo->addMethod($constructor);

var_dump($classInfo->getMethods());