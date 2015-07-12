<?php

namespace CodeAnvil;

use CodeAnvil\Info\ConstantInfo;
use CodeAnvil\Info\InterfaceInfo;
use CodeAnvil\Info\MethodInfo;
use CodeAnvil\Stubs\BarInterface;
use CodeAnvil\Stubs\FooInterface;

class InterfaceCodeGeneratorTest extends ReflectionTestCase {

    protected static function getTmpSubDirectory() {
        return 'interface';
    }

    public function testCreateInterfaceType() {
        $info = (new InterfaceInfo())->setName('InterfaceType');

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'type');

        $this->assertTypeIsInterface('InterfaceType');
    }

    public function testInterfaceWithNamespace() {
        $info = (new InterfaceInfo())->setNamespace('Foo\\Bar')->setName('InterfaceType');

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'namespace');

        $this->assertTypeHasNamespace('Foo\\Bar', 'Foo\\Bar\\InterfaceType');
    }

    public function testExtendingInterfaces() {
        $info = (new InterfaceInfo())->setName('InterfaceExtendsInterfaces');
        $info->addExtendedInterface(BarInterface::class)->addExtendedInterface(FooInterface::class);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'extend_interfaces');

        $this->assertTypeImplementsInterface(BarInterface::class, 'InterfaceExtendsInterfaces');
        $this->assertTypeImplementsInterface(FooInterface::class, 'InterfaceExtendsInterfaces');
    }

    public function testInterfaceWithConstant() {
        $foo = (new ConstantInfo())->setName('FOO')->setDefaultValue('123');
        $bar = (new ConstantInfo())->setName('BAR')->setDefaultValue(789);
        $info = (new InterfaceInfo())->setName('InterfaceWithConstants');
        $info->addConstant($foo)->addConstant($bar);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'constants');

        $this->assertTypeHasConstant('FOO', 'InterfaceWithConstants');
        $this->assertTypeConstantHasValue('123', 'InterfaceWithConstants', 'FOO');
        $this->assertTypeHasConstant('BAR', 'InterfaceWithConstants');
        $this->assertTypeConstantHasValue(789, 'InterfaceWithConstants', 'BAR');
    }

    public function testInterfaceWithMethods() {
        $foo = (new MethodInfo())->setName('foo');
        $bar = (new MethodInfo())->setName('bar');
        $baz = (new MethodInfo())->setName('baz');

        $info = (new InterfaceInfo())->setName('InterfaceWithMethods');
        $info->addMethod($foo)->addMethod($bar)->addMethod($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods');

        $this->assertTypeHasMethod('foo', 'InterfaceWithMethods');
        $this->assertTypeHasMethod('bar', 'InterfaceWithMethods');
        $this->assertTypeHasMethod('baz', 'InterfaceWithMethods');
    }

}