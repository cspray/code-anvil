<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use CodeAnvil\Info\{ClassInfo, ConstantInfo, PropertyInfo, MethodInfo, ParameterInfo};

$fooConst = (new ConstantInfo())->setName('FOO')->setDefaultValue([1,2,3]);
$barConst = (new ConstantInfo())->setName('BAR')->setDefaultValue(123);

$fooProp = (new PropertyInfo())->setName('foo')->setDefaultValue('string');
$barProp = (new PropertyInfo())->setName('bar')->setVisibility('protected')->setDefaultValue(false);
$fooBarProp = (new PropertyInfo())->setName('fooBar')->setVisibility('private')->setDefaultValue(true);

$createMethod = (new \CodeAnvil\Info\MethodInfo())->setName('create')
                                                  ->makeStatic()
                                                  ->setReturnType('KitchenSinkClass')
                                                  ->setBody('return new KitchenSinkClass();');

/** @var CodeAnvil\Info\MethodInfo $getBar */
$getBar = (new MethodInfo())->setName('getBar')->setBody('return $this->bar;');

$barParam = (new ParameterInfo())->setName('bar')->setTypeDeclaration('string');
$setBar = (new MethodInfo())->setName('setBar')
                            ->setReturnType('self')
                            ->addParameter($barParam)
                            ->setBody("\$this->bar = \$bar;\n\t\treturn \$this;");

/** @var CodeAnvil\Info\ClassInfo $class */
$class = new ClassInfo();
$class->setName('KitchenSinkClass')
      ->addConstant($fooConst)
      ->addConstant($barConst)
      ->addProperty($fooProp)
      ->addProperty($barProp)
      ->addProperty($fooBarProp)
      ->addMethod($createMethod)
      ->addMethod($getBar)
      ->addMethod($setBar);

echo "KITCHEN SINK EXAMPLE:\n";
echo str_repeat('=', 80);
echo "\n\n";
echo "The code generated:\n";
echo $code = (new \CodeAnvil\CodeGenerator())->generate($class);
echo "\n\n";
echo "The class in action:\n";

$path = tempnam(sys_get_temp_dir(), 'code_anvil_kitchen_sink_example');
$h = fopen($path, 'w');
fwrite($h, $code);
fclose($h);

require_once $path;

$example = KitchenSinkClass::create();

echo "First bar = " . var_export($example->getBar(), true) . "\n";
echo "Provide some input to set to bar:";

$h = fopen('php://stdin', 'r');
$newBar = trim(fgets($h), "\n");

$example->setBar($newBar);
echo "Second bar = " . var_export($example->getBar(), true) . "\n\n";
echo "Object dump:\n";
var_dump($example);