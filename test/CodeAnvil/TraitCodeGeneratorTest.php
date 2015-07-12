<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace CodeAnvil;

use CodeAnvil\Info\MethodInfo;
use CodeAnvil\Info\ParameterInfo;
use CodeAnvil\Info\PropertyInfo;
use CodeAnvil\Info\TraitInfo;
use CodeAnvil\Stubs\BarInterface;
use CodeAnvil\Stubs\BazInterface;

class TraitCodeGeneratorTest extends ReflectionTestCase {

    public static function getTmpSubDirectory() {
        return 'trait';
    }

    public function testTypeIsTrait() {
        $trait = (new TraitInfo())->setName('FooTrait');
        $code = (new CodeGenerator())->generate($trait);
        $this->requireCodeFromString($code, 'type');

        $this->assertTypeIsTrait('FooTrait');
    }

    public function testTraitHasNamespace() {
        $trait = (new TraitInfo())->setNamespace('Foo\\Bar')->setName('NamespacedTrait');
        $code = (new CodeGenerator())->generate($trait);
        $this->requireCodeFromString($code, 'namespace');

        $this->assertTypeHasNamespace('Foo\\Bar', 'Foo\\Bar\\NamespacedTrait');
    }

    public function testGeneratingDefinedProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')->setName('foo');
        $bar = (new PropertyInfo())->setVisibility('protected')->setName('bar');
        $baz = (new PropertyInfo())->setVisibility('private')->setName('baz');

        $info = (new TraitInfo())->setName('PropertyTrait')->addProperty($foo)->addProperty($bar)->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'properties');

        $this->assertPropertyIsPublic('foo', 'PropertyTrait');
        $this->assertPropertyIsProtected('bar', 'PropertyTrait');
        $this->assertPropertyIsPrivate('baz', 'PropertyTrait');
    }

    public function testGeneratingDefaultProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')->setName('foo')->setDefaultValue(true);
        $bar = (new PropertyInfo())->setVisibility('protected')->setName('bar')->setDefaultValue([1, 2, 3]);
        $baz = (new PropertyInfo())->setVisibility('private')->setName('baz')->setDefaultValue('oh yea');


        $info = (new TraitInfo())->setName('PropertyDefaultValueTrait')
            ->addProperty($foo)
            ->addProperty($bar)
            ->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'property_default_values');

        $this->assertPropertyHasDefaultValue(true, 'PropertyDefaultValueTrait', 'foo');
        $this->assertPropertyHasDefaultValue([1,2,3], 'PropertyDefaultValueTrait', 'bar');
        $this->assertPropertyHasDefaultValue('oh yea', 'PropertyDefaultValueTrait', 'baz');
    }

    public function testGeneratingStaticProperties() {
        $foo = (new PropertyInfo())->setVisibility('public')->setName('foo')->makeStatic();
        $bar = (new PropertyInfo())->setVisibility('protected')->setName('bar')->makeStatic();
        $baz = (new PropertyInfo())->setVisibility('private')->setName('baz')->makeStatic();

        $info = (new TraitInfo())->setName('StaticPropertyTrait')->addProperty($foo)->addProperty($bar)->addProperty($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'static_properties');

        $this->assertPropertyIsStatic('foo', 'StaticPropertyTrait');
        $this->assertPropertyIsStatic('bar', 'StaticPropertyTrait');
        $this->assertPropertyIsStatic('baz', 'StaticPropertyTrait');
    }

    public function testGeneratingSimpleMethod() {
        $foo = (new MethodInfo())->setName('foo');
        $bar = (new MethodInfo())->setName('bar');
        $baz = (new MethodInfo())->setName('baz');

        $info = (new TraitInfo())->setName('TraitWithSimpleMethods')->addMethod($foo)->addMethod($bar)->addMethod($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods_no_parameters');

        $this->assertTypeHasMethod('foo', 'TraitWithSimpleMethods');
        $this->assertTypeHasMethod('bar', 'TraitWithSimpleMethods');
        $this->assertTypeHasMethod('baz', 'TraitWithSimpleMethods');
    }

    public function testGeneratingMethodsWithCorrectVisibility() {
        $foo = (new MethodInfo())->setName('foo')->setVisibility('public');
        $bar = (new MethodInfo())->setName('bar')->setVisibility('protected');
        $baz = (new MethodInfo())->setName('baz')->setVisibility('private');

        $info = (new TraitInfo())->setName('TraitWithVisibleMethods')->addMethod($foo)->addMethod($bar)->addMethod($baz);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'methods_visibility');

        $this->assertMethodIsPublic('foo', 'TraitWithVisibleMethods');
        $this->assertMethodIsProtected('bar', 'TraitWithVisibleMethods');
        $this->assertMethodIsPrivate('baz', 'TraitWithVisibleMethods');
    }

    public function testGeneratingStaticMethod() {
        $foo = (new MethodInfo())->setName('foo')->makeStatic();
        $info = (new TraitInfo())->setName('TraitWithStaticMethod')->addMethod($foo);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'static_methods');

        $this->assertMethodIsStatic('foo', 'TraitWithStaticMethod');
    }

    public function testGeneratingFinalMethod() {
        $foo = (new MethodInfo())->setName('foo')->makeFinal();
        $info = (new TraitInfo())->setName('TraitWithFinalMethod')->addMethod($foo);

        $code = (new CodeGenerator())->generate($info);
        $this->requireCodeFromString($code, 'final_methods');

        $this->assertMethodIsFinal('foo', 'TraitWithFinalMethod');
    }

    public function testGeneratingMethodWithParameters() {
        $param0 = (new ParameterInfo())->setName('bar');
        $param1 = (new ParameterInfo())->setName('baz');
        $param2 = (new ParameterInfo())->setName('qux');
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0)->addParameter($param1)->addParameter($param2);

        $class = (new TraitInfo())->setName('TraitWithMethodAndParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_parameters');

        $this->assertMethodParameterIsPresent(0, 'TraitWithMethodAndParameters', 'foo');
        $this->assertMethodParameterIsPresent(1, 'TraitWithMethodAndParameters', 'foo');
        $this->assertMethodParameterIsPresent(2, 'TraitWithMethodAndParameters', 'foo');
    }

    public function testGeneratingMethodWithDefaultParameters() {
        $param0 = (new ParameterInfo())->setName('bar')->setDefaultValue([]);
        $param1 = (new ParameterInfo())->setName('baz')->setDefaultValue('string');
        $param2 = (new ParameterInfo())->setName('qux')->setDefaultValue(null);
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0)->addParameter($param1)->addParameter($param2);

        $class = (new TraitInfo())->setName('TraitWithMethodAndDefaultParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_default_parameters');

        $this->assertMethodParameterHasDefaultValue([], 'TraitWithMethodAndDefaultParameters', 'foo', 0);
        $this->assertMethodParameterHasDefaultValue('string', 'TraitWithMethodAndDefaultParameters', 'foo', 1);
        $this->assertMethodParameterHasDefaultValue(null, 'TraitWithMethodAndDefaultParameters', 'foo', 2);
    }

    public function testEnsuringRequiredParametersAreRequired() {
        $param0 = (new ParameterInfo())->setName('bar');
        $param1 = (new ParameterInfo())->setName('baz');
        $param2 = (new ParameterInfo())->setName('qux')->setDefaultValue(null);
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0)->addParameter($param1)->addParameter($param2);

        $class = (new TraitInfo())->setName('TraitWithMethodAndRequiredParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_required_parameters');

        $this->assertMethodParameterIsRequired(0, 'TraitWithMethodAndRequiredParameters', 'foo');
        $this->assertMethodParameterIsRequired(1, 'TraitWithMethodAndRequiredParameters', 'foo');
        $this->assertMethodParameterHasDefaultValue(null, 'TraitWithMethodAndRequiredParameters', 'foo', 2);
    }

    public function testGeneratingMethodWithVariadicParameter() {
        $param0 = (new ParameterInfo())->setName('bar')->makeVariadic();
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0);

        $class = (new TraitInfo())->setName('TraitWithMethodAndVariadicParameter')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_variadic_parameter');

        $this->assertMethodParameterIsVariadic(0, 'TraitWithMethodAndVariadicParameter', 'foo');
    }

    public function testGeneratingMethodWithTypeDeclarations() {
        $param0 = (new ParameterInfo())->setName('bar')->setTypeDeclaration('string');
        $param1 = (new ParameterInfo())->setName('baz')->setTypeDeclaration(BarInterface::class);
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0)->addParameter($param1);

        $class = (new TraitInfo())->setName('TraitWithMethodAndTypeDeclarationParameters')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_type_declaration_parameters');

        $this->assertMethodParameterHasTypeDeclaration('string', 'TraitWithMethodAndTypeDeclarationParameters', 'foo', 0);
        $this->assertMethodParameterHasTypeDeclaration(BarInterface::class, 'TraitWithMethodAndTypeDeclarationParameters', 'foo', 1);
    }

    public function testGeneratingMethodWithReturnType() {
        $foo = (new MethodInfo())->setName('foo')->setReturnType('array');
        $bar = (new MethodInfo())->setName('bar')->setReturnType(BarInterface::class);

        $class = (new TraitInfo())->setName('TraitMethodWithReturnType')->addMethod($foo)->addMethod($bar);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_with_return_type');

        $this->assertMethodHasReturnType('array', 'TraitMethodWithReturnType', 'foo');
        $this->assertMethodHasReturnType(BarInterface::class, 'TraitMethodWithReturnType', 'bar');
    }

    public function testGeneratingMethodHasByReferenceParameter() {
        $param0 = (new ParameterInfo())->setName('bar')->makeByReference();
        $foo = (new MethodInfo())->setName('foo')->addParameter($param0);

        $class = (new TraitInfo())->setName('TraitWithMethodParameterByReference')->addMethod($foo);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_parameter_by_reference');

        $this->assertMethodParameterIsPassedByReference(0, 'TraitWithMethodParameterByReference', 'foo');
    }

    public function testGeneratingPropertyWithDefaultFalseValue() {
        $falseProperty = (new PropertyInfo())->setName('foo')->setDefaultValue(false);
        $nullProperty = (new PropertyInfo())->setName('bar')->setDefaultValue(null);
        $strProperty = (new PropertyInfo())->setName('fooBar')->setDefaultValue('');
        $noDefaultProperty = (new PropertyInfo())->setName('fooBarBaz');

        $class = (new TraitInfo())->setName('TraitWithFalsePropertyDefaultValues')
                                  ->addProperty($falseProperty)
                                  ->addProperty($nullProperty)
                                  ->addProperty($strProperty)
                                  ->addProperty($noDefaultProperty);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'false_property_default_values');

        $this->assertPropertyHasDefaultValue(false, 'TraitWithFalsePropertyDefaultValues', 'foo');
        $this->assertPropertyHasDefaultValue(null, 'TraitWithFalsePropertyDefaultValues', 'bar');
        $this->assertPropertyHasDefaultValue('', 'TraitWithFalsePropertyDefaultValues', 'fooBar');
        $this->assertPropertyHasDefaultValue(null, 'TraitWithFalsePropertyDefaultValues', 'fooBarBaz');
    }

    public function testGeneratingUsesWithTypeDeclarationUsedMultipleTimes() {
        $method1 = (new MethodInfo())->setName('foo')->setReturnType(BazInterface::class);
        $method2 = (new MethodInfo())->setName('bar')->setReturnType(BazInterface::class);

        $class = (new TraitInfo())->setName('TraitWithUsesMultipleTimes')->addMethod($method1)->addMethod($method2);

        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'use_multiple_declarations');

        $this->assertTypeHasMethod('foo', 'TraitWithUsesMultipleTimes');
        $this->assertTypeHasMethod('bar', 'TraitWithUsesMultipleTimes');
        // The real test is to manually confirm the generated file has the appropriate use statements declared.
    }

    public function testGeneratingDocComment() {
        $docComment = <<<TEXT
/**
 * Some doc block comment for things
 */
TEXT;

        $class = (new TraitInfo())->setName('TraitWithDocComment')->setDocComment($docComment);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'doc_comment');

        $this->assertTypeHasDocComment($docComment, 'TraitWithDocComment');
    }

    public function testGeneratingPropertyDocComment() {
        $propertyDocComment = <<<TEXT
/**
 * Some doc block comment
 */
TEXT;

        $property = (new PropertyInfo())->setName('foo')->setDocComment($propertyDocComment);
        $class = (new TraitInfo())->setName('TraitWithPropertyDocComment')->addProperty($property);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'property_doc_comment');

        $expected = str_replace("\n", "\n\t", $propertyDocComment);

        $this->assertPropertyHasDocComment($expected, 'TraitWithPropertyDocComment', 'foo');
    }

    public function testGeneratingMethodDocComment() {
        $docComment = <<<TEXT
/**
 * Some doc block comment
 */
TEXT;

        $method = (new MethodInfo())->setName('foo')->setDocComment($docComment);
        $class = (new TraitInfo())->setName('TraitWithMethodDocComment')->addMethod($method);
        $code = (new CodeGenerator())->generate($class);
        $this->requireCodeFromString($code, 'method_doc_comment');

        $expected = str_replace("\n", "\n\t", $docComment);

        $this->assertMethodHasDocComment($expected, 'TraitWithMethodDocComment', 'foo');
    }

    public function testGeneratingStrictTypeClass() {
        $class = (new TraitInfo())->setName('TraitWithStrictType')->declareStrict();
        $code = (new CodeGenerator())->generate($class);

        $this->assertSame('declare(strict_types=1);', explode("\n", $code)[2]);
    }

}