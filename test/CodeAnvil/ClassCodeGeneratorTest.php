<?php

declare(strict_types=1);

namespace CodeAnvil;

use CodeAnvil\Info\ClassInfo;
use CodeAnvil\Info\ConstantInfo;
use CodeAnvil\Info\MethodInfo;
use CodeAnvil\Info\ParameterInfo;
use CodeAnvil\Info\PropertyInfo;
use CodeAnvil\Stubs\{ParentStub, BarInterface, BazInterface, FooInterface};

class ClassCodeGeneratorTest extends ReflectionTestCase {

    protected static function getTmpSubDirectory() {
        return 'class';
    }

    public function testGeneratingEmptyClass() {
        $info = (new ClassInfo())->setName('Foo');
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'empty');

        $subject = new \Foo();

        $this->assertInstanceOf('Foo', $subject);
    }

    public function testGeneratingFinalClass() {
        $info = (new ClassInfo())->setName('FooFinal')->makeFinal();
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'final');

        $this->assertClassIsFinal('FooFinal');
    }

    public function testGeneratingAbstractClass() {
        $info = (new ClassInfo())->setName('FooAbstract')->makeAbstract();
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'abstract');

        $this->assertClassIsAbstract('FooAbstract');
    }

    public function testGeneratingClassWithNamespace() {
        $info = (new ClassInfo())->setNamespace('Foo\\Bar')->setName('Qux');
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'namespaced');

        $this->assertTypeHasNamespace('Foo\\Bar', 'Foo\\Bar\\Qux');
    }

    public function testGeneratingClassWithParent() {
        $info = (new ClassInfo())->setNamespace('Foo\\Bar')
                                 ->setName('ChildClass')
                                 ->setParentClass(ParentStub::class);
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'child');

        $this->assertClassExtendsParent(ParentStub::class, 'Foo\\Bar\\ChildClass');
    }

    public function testGeneratingClassWithImplementedInterfaces() {
        $info = (new ClassInfo())->setName('LotsOInterfaces')
            ->addImplementedInterface(BarInterface::class)
            ->addImplementedInterface(BazInterface::class)
            ->addImplementedInterface(FooInterface::class);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'implemented_interfaces');

        $this->assertTypeImplementsInterface(BarInterface::class, 'LotsOInterfaces');
        $this->assertTypeImplementsInterface(BazInterface::class, 'LotsOInterfaces');
        $this->assertTypeImplementsInterface(FooInterface::class, 'LotsOInterfaces');
    }

    public function testGeneratingClassWithConstants() {
        $aConst = (new ConstantInfo())->setName('A');
        $bConst = (new ConstantInfo())->setName('B');
        $cConst = (new ConstantInfo())->setName('C');

        $info = (new ClassInfo())->setName('ConstantClass')
                                 ->addConstant($aConst)
                                 ->addConstant($bConst)
                                 ->addConstant($cConst);
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'constants');

        $this->assertTypeHasConstant('A', 'ConstantClass');
        $this->assertTypeHasConstant('B', 'ConstantClass');
        $this->assertTypeHasConstant('C', 'ConstantClass');
    }

    public function testGeneratingClassWithConstantValues() {
        $aConst = (new ConstantInfo())->setName('A')->setDefaultValue(true);
        $bConst = (new ConstantInfo())->setName('B')->setDefaultValue(1);
        $cConst = (new ConstantInfo())->setName('C')->setDefaultValue('foo');

        $info = (new ClassInfo())->setName('ConstantValuesClass')
                                 ->addConstant($aConst)
                                 ->addConstant($bConst)
                                 ->addConstant($cConst);
        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'constant_values');

        $this->assertTypeConstantHasValue(true, 'ConstantValuesClass', 'A');
        $this->assertTypeConstantHasValue(1, 'ConstantValuesClass', 'B');
        $this->assertTypeConstantHasValue('foo', 'ConstantValuesClass', 'C');
    }

    public function testGeneratingClassWithDefinedProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')
                                    ->setName('foo');
        $bar = (new PropertyInfo())->setVisibility('protected')
                                   ->setName('bar');
        $baz = (new PropertyInfo())->setVisibility('private')
                                   ->setName('baz');

        $info = (new ClassInfo())->setName('PropertyClass')
                                 ->addProperty($foo)
                                 ->addProperty($bar)
                                 ->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'properties');

        $this->assertPropertyIsPublic('foo', 'PropertyClass');
        $this->assertPropertyIsProtected('bar', 'PropertyClass');
        $this->assertPropertyIsPrivate('baz', 'PropertyClass');
    }

    public function testGeneratingClassWithDefaultProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')
                                   ->setName('foo')
                                   ->setDefaultValue(true);
        $bar = (new PropertyInfo())->setVisibility('protected')
                                   ->setName('bar')
                                   ->setDefaultValue([1, 2, 3]);
        $baz = (new PropertyInfo())->setVisibility('private')
                                   ->setName('baz')
                                   ->setDefaultValue('oh yea');


        $info = (new ClassInfo())->setName('PropertyDefaultValueClass')
                                 ->addProperty($foo)
                                 ->addProperty($bar)
                                 ->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'property_default_values');

        $this->assertPropertyHasDefaultValue(true, 'PropertyDefaultValueClass', 'foo');
        $this->assertPropertyHasDefaultValue([1,2,3], 'PropertyDefaultValueClass', 'bar');
        $this->assertPropertyHasDefaultValue('oh yea', 'PropertyDefaultValueClass', 'baz');
    }

    public function testGeneratingClassWithStaticProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')
                                   ->setName('foo')
                                   ->makeStatic();
        $bar = (new PropertyInfo())->setVisibility('protected')
                                   ->setName('bar')
                                   ->makeStatic();
        $baz = (new PropertyInfo())->setVisibility('private')
                                   ->setName('baz')
                                   ->makeStatic();

        $info = (new ClassInfo())->setName('StaticPropertyClass')
                                 ->addProperty($foo)
                                 ->addProperty($bar)
                                 ->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'static_properties');

        $this->assertPropertyIsStatic('foo', 'StaticPropertyClass');
        $this->assertPropertyIsStatic('bar', 'StaticPropertyClass');
        $this->assertPropertyIsStatic('baz', 'StaticPropertyClass');
    }

    public function testGeneratingClassWithSimpleMethod() {
        $foo = (new MethodInfo())->setName('foo');
        $bar = (new MethodInfo())->setName('bar');
        $baz = (new MethodInfo())->setName('baz');

        $info = (new ClassInfo())->setName('ClassWithSimpleMethods')
                                 ->addMethod($foo)
                                 ->addMethod($bar)
                                 ->addMethod($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods_no_parameters');

        $this->assertTypeHasMethod('foo', 'ClassWithSimpleMethods');
        $this->assertTypeHasMethod('bar', 'ClassWithSimpleMethods');
        $this->assertTypeHasMethod('baz', 'ClassWithSimpleMethods');
    }

    public function testGeneratingClassMethodsWithCorrectVisibility() {
        $foo = (new MethodInfo())->setName('foo')->setVisibility('public');
        $bar = (new MethodInfo())->setName('bar')->setVisibility('protected');
        $baz = (new MethodInfo())->setName('baz')->setVisibility('private');

        $info = (new ClassInfo())->setName('ClassWithVisibleMethods')
                                 ->addMethod($foo)
                                 ->addMethod($bar)
                                 ->addMethod($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods_visibility');

        $this->assertMethodIsPublic('foo', 'ClassWithVisibleMethods');
        $this->assertMethodIsProtected('bar', 'ClassWithVisibleMethods');
        $this->assertMethodIsPrivate('baz', 'ClassWithVisibleMethods');
    }

    public function testGeneratingMethodWithBody() {
        $foo = (new MethodInfo())->setName('foo')
                                 ->setVisibility('public')
                                 ->setBody('return "oh yea";');
        $info = (new ClassInfo())->setName('ClassWithFunctioningMethod')->addMethod($foo);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods_function_body');

        $o = new \ClassWithFunctioningMethod();
        $this->assertSame('oh yea', $o->foo());
    }

    public function testGeneratingStaticMethod() {
        $foo = (new MethodInfo())->setName('foo')
                                 ->makeStatic();
        $info = (new ClassInfo())->setName('ClassWithStaticMethod')->addMethod($foo);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'static_methods');

        $this->assertMethodIsStatic('foo', 'ClassWithStaticMethod');
    }

    public function testGeneratingFinalMethod() {
        $foo = (new MethodInfo())->setName('foo')->makeFinal();
        $info = (new ClassInfo())->setName('ClassWithFinalMethod')->addMethod($foo);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'final_methods');

        $this->assertMethodIsFinal('foo', 'ClassWithFinalMethod');
    }

    public function testGeneratingMethodWithParameters() {
        $param0 = (new ParameterInfo())->setName('bar');
        $param1 = (new ParameterInfo())->setName('baz');
        $param2 = (new ParameterInfo())->setName('qux');
        $foo = (new MethodInfo())->setName('foo')
                                 ->addParameter($param0)
                                 ->addParameter($param1)
                                 ->addParameter($param2);

        $class = (new ClassInfo())->setName('ClassWithMethodAndParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_parameters');

        $this->assertMethodParameterIsPresent(0, 'ClassWithMethodAndParameters', 'foo');
        $this->assertMethodParameterIsPresent(1, 'ClassWithMethodAndParameters', 'foo');
        $this->assertMethodParameterIsPresent(2, 'ClassWithMethodAndParameters', 'foo');
    }

    public function testGeneratingMethodWithDefaultParameters() {
        $param0 = (new ParameterInfo())->setName('bar')->setDefaultValue([]);
        $param1 = (new ParameterInfo())->setName('baz')->setDefaultValue('string');
        $param2 = (new ParameterInfo())->setName('qux')->setDefaultValue(null);
        $foo = (new MethodInfo())->setName('foo')
            ->addParameter($param0)
            ->addParameter($param1)
            ->addParameter($param2);

        $class = (new ClassInfo())->setName('ClassWithMethodAndDefaultParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_default_parameters');

        $this->assertMethodParameterHasDefaultValue([], 'ClassWithMethodAndDefaultParameters', 'foo', 0);
        $this->assertMethodParameterHasDefaultValue('string', 'ClassWithMethodAndDefaultParameters', 'foo', 1);
        $this->assertMethodParameterHasDefaultValue(null, 'ClassWithMethodAndDefaultParameters', 'foo', 2);
    }

    public function testEnsuringRequiredParametersAreRequired() {
        $param0 = (new ParameterInfo())->setName('bar');
        $param1 = (new ParameterInfo())->setName('baz');
        $param2 = (new ParameterInfo())->setName('qux')->setDefaultValue(null);
        $foo = (new MethodInfo())->setName('foo')
            ->addParameter($param0)
            ->addParameter($param1)
            ->addParameter($param2);

        $class = (new ClassInfo())->setName('ClassWithMethodAndRequiredParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_required_parameters');

        $this->assertMethodParameterIsRequired(0, 'ClassWithMethodAndRequiredParameters', 'foo');
        $this->assertMethodParameterIsRequired(1, 'ClassWithMethodAndRequiredParameters', 'foo');
        $this->assertMethodParameterHasDefaultValue(null, 'ClassWithMethodAndRequiredParameters', 'foo', 2);
    }

    public function testGeneratingMethodWithVariadicParameter() {
        $param0 = (new ParameterInfo())->setName('bar')->makeVariadic();
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0);

        $class = (new ClassInfo())->setName('ClassWithMethodAndVariadicParameter')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_variadic_parameter');

        $this->assertMethodParameterIsVariadic(0, 'ClassWithMethodAndVariadicParameter', 'foo');
    }

    public function testGeneratingMethodWithTypeDeclarations() {
        $param0 = (new ParameterInfo())->setName('bar')->setTypeDeclaration('string');
        $param1 = (new ParameterInfo())->setName('baz')->setTypeDeclaration(BarInterface::class);
        $foo = (new MethodInfo())->setName('foo')
            ->addParameter($param0)
            ->addParameter($param1);

        $class = (new ClassInfo())->setName('ClassWithMethodAndTypeDeclarationParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_type_declaration_parameters');

        $this->assertMethodParameterHasTypeDeclaration('string', 'ClassWithMethodAndTypeDeclarationParameters', 'foo', 0);
        $this->assertMethodParameterHasTypeDeclaration(BarInterface::class, 'ClassWithMethodAndTypeDeclarationParameters', 'foo', 1);
    }

    public function testGeneratingMethodWithReturnType() {
        $foo = (new MethodInfo())->setName('foo')->setReturnType('array');
        $bar = (new MethodInfo())->setName('bar')->setReturnType(BarInterface::class);

        $class = (new ClassInfo())->setName('ClassMethodWithReturnType')->addMethod($foo)->addMethod($bar);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_return_type');

        $this->assertMethodHasReturnType('array', 'ClassMethodWithReturnType', 'foo');
        $this->assertMethodHasReturnType(BarInterface::class, 'ClassMethodWithReturnType', 'bar');
    }

    public function testGeneratingMethodHasByReferenceParameter() {
        $param0 = (new ParameterInfo())->setName('bar')->makeByReference();
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0);

        $class = (new ClassInfo())->setName('ClassWithMethodParameterByReference')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_parameter_by_reference');

        $this->assertMethodParameterIsPassedByReference(0, 'ClassWithMethodParameterByReference', 'foo');
    }

    public function testGeneratingPropertyWithDefaultFalseValue() {
        $falseProperty = (new PropertyInfo())->setName('foo')->setDefaultValue(false);
        $nullProperty = (new PropertyInfo())->setName('bar')->setDefaultValue(null);
        $strProperty = (new PropertyInfo())->setName('fooBar')->setDefaultValue('');
        $noDefaultProperty = (new PropertyInfo())->setName('fooBarBaz');

        $class = (new ClassInfo())->setName('ClassWithFalsePropertyDefaultValues')
                                  ->addProperty($falseProperty)
                                  ->addProperty($nullProperty)
                                  ->addProperty($strProperty)
                                  ->addProperty($noDefaultProperty);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'false_property_default_values');

        $this->assertPropertyHasDefaultValue(false, 'ClassWithFalsePropertyDefaultValues', 'foo');
        $this->assertPropertyHasDefaultValue(null, 'ClassWithFalsePropertyDefaultValues', 'bar');
        $this->assertPropertyHasDefaultValue('', 'ClassWithFalsePropertyDefaultValues', 'fooBar');
        $this->assertPropertyHasDefaultValue(null, 'ClassWithFalsePropertyDefaultValues', 'fooBarBaz');
    }

    public function testGeneratingUsesWithSameNameAsClass() {
        $method = (new MethodInfo())->setName('foo')->setReturnType('ClassWithUsesSameNameAsClass');

        $class = (new ClassInfo())->setName('ClassWithUsesSameNameAsClass')->addMethod($method);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'use_same_name_as_class');

        $this->assertTypeHasMethod('foo', 'ClassWithUsesSameNameAsClass');
        // The real test is to manually confirm the generated file has the appropriate use statements declared.
    }

    public function testGeneratingUsesWithTypeDeclarationUsedMultipleTimes() {
        $method1 = (new MethodInfo())->setName('foo')->setReturnType(BazInterface::class);
        $method2 = (new MethodInfo())->setName('bar')->setReturnType(BazInterface::class);

        $class = (new ClassInfo())->setName('ClassWithUsesMultipleTimes')->addMethod($method1)->addMethod($method2);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'use_multiple_declarations');

        $this->assertTypeHasMethod('foo', 'ClassWithUsesMultipleTimes');
        $this->assertTypeHasMethod('bar', 'ClassWithUsesMultipleTimes');
        // The real test is to manually confirm the generated file has the appropriate use statements declared.
    }

    public function testGeneratingDocComment() {
        $docComment = <<<TEXT
/**
 * Some doc block comment for things
 */
TEXT;

        $class = (new ClassInfo())->setName('ClassWithDocComment')->setDocComment($docComment);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'doc_comment');

        $this->assertTypeHasDocComment($docComment, 'ClassWithDocComment');
    }

    public function testGeneratingPropertyDocComment() {
        $propertyDocComment = <<<TEXT
/**
 * Some doc block comment
 */
TEXT;

        $property = (new PropertyInfo())->setName('foo')->setDocComment($propertyDocComment);
        $class = (new ClassInfo())->setName('ClassWithPropertyDocComment')->addProperty($property);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'property_doc_comment');

        $expected = str_replace("\n", "\n\t", $propertyDocComment);

        $this->assertPropertyHasDocComment($expected, 'ClassWithPropertyDocComment', 'foo');
    }

    public function testGeneratingMethodDocComment() {
        $docComment = <<<TEXT
/**
 * Some doc block comment
 */
TEXT;

        $method = (new MethodInfo())->setName('foo')->setDocComment($docComment);
        $class = (new ClassInfo())->setName('ClassWithMethodDocComment')->addMethod($method);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_doc_comment');

        $expected = str_replace("\n", "\n\t", $docComment);

        $this->assertMethodHasDocComment($expected, 'ClassWithMethodDocComment', 'foo');
    }

    public function testGeneratingStrictTypeClass() {
        $class = (new ClassInfo())->setName('ClassWithStrictType')->declareStrict();
        $code = (new CodeGenerator())->generate($class);

        $this->assertSame('declare(strict_types=1);', explode("\n", $code)[2]);
    }

}