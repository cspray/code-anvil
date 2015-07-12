<?php

declare(strict_types=1);

namespace CodeAnvil;

use CodeAnvil\Constraints\MethodHasDocComment;
use CodeAnvil\Constraints\PropertyHasDocComment;
use CodeAnvil\Constraints\TypeConstantHasValue;
use CodeAnvil\Constraints\TypeHasConstant;
use CodeAnvil\Constraints\TypeHasDocComment;
use CodeAnvil\Constraints\TypeHasMethod;
use CodeAnvil\Constraints\TypeHasNamespace;
use CodeAnvil\Constraints\ClassHasParent;
use CodeAnvil\Constraints\TypeImplementsInterface;
use CodeAnvil\Constraints\ClassIsAbstract;
use CodeAnvil\Constraints\ClassIsFinal;
use CodeAnvil\Constraints\MethodIsPrivate;
use CodeAnvil\Constraints\MethodIsProtected;
use CodeAnvil\Constraints\MethodIsPublic;
use CodeAnvil\Constraints\MethodHasReturnType;
use CodeAnvil\Constraints\MethodIsFinal;
use CodeAnvil\Constraints\MethodIsStatic;
use CodeAnvil\Constraints\MethodParameterHasDefaultValue;
use CodeAnvil\Constraints\MethodParameterHasTypeDeclaration;
use CodeAnvil\Constraints\MethodParameterIsByReference;
use CodeAnvil\Constraints\MethodParameterIsPresent;
use CodeAnvil\Constraints\MethodParameterIsRequired;
use CodeAnvil\Constraints\MethodParameterIsVariadic;
use CodeAnvil\Constraints\PropertyHasDefaultValue;
use CodeAnvil\Constraints\PropertyIsPrivate;
use CodeAnvil\Constraints\PropertyIsProtected;
use CodeAnvil\Constraints\PropertyIsPublic;
use CodeAnvil\Constraints\PropertyIsStatic;
use CodeAnvil\Constraints\TypeIsinterface;
use CodeAnvil\Constraints\TypeIsTrait;
use PHPUnit_Framework_TestCase as UnitTestCase;

abstract class ReflectionTestCase extends UnitTestCase {

    /**
     * @return string
     */
    protected static function getTmpSubDirectory() {
        throw new \RuntimeException('You must set the tmp sub-directory in overriding classes.');
    }

    public static function setUpBeforeClass() {
        $dir = sprintf("%s/tmp/%s/*", dirname(dirname(__DIR__)), static::getTmpSubDirectory());

        $files = glob($dir);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function requireCodeFromString($code, $className) {
        $dir = sprintf('%s/tmp/%s', dirname(dirname(__DIR__)), static::getTmpSubDirectory());
        $path = tempnam($dir, 'code_generator_' . $className . '_');

        $h = fopen($path, 'w');
        fwrite($h, $code);
        fclose($h);

        return require_once $path;
    }

    // TYPE ASSERTIONS
    // =================================================================================================================
    // Assertions that can be used to test an interface, class, or trait.

    public function assertTypeIsInterface(string $type) {
        self::assertThat($type, new TypeIsinterface());
    }

    public function assertTypeIsTrait(string $type) {
        self::assertThat($type, new TypeIsTrait());
    }

    public function assertTypeHasNamespace(string $namespace, string $type) {
        self::assertThat($type, new TypeHasNamespace($namespace));
    }

    public function assertTypeImplementsInterface(string $interface, string $type) {
        self::assertThat($type, new TypeImplementsInterface($interface));
    }

    public function assertTypeHasConstant(string $const, string $type) {
        self::assertThat($type, new TypeHasConstant($const));
    }

    public function assertTypeConstantHasValue($expected, string $className, string $constName) {
        self::assertThat($expected, new TypeConstantHasValue($className, $constName));
    }

    public function assertTypeHasMethod(string $methodName, string $className) {
        self::assertThat($methodName, new TypeHasMethod($className));
    }

    public function assertTypeHasDocComment(string $docComment, string $typeName) {
        self::assertThat($docComment, new TypeHasDocComment($typeName));
    }

    // CLASS ASSERTIONS
    // =================================================================================================================
    // Assertion that can only be run against classes.

    public function assertClassIsFinal(string $className) {
        self::assertThat($className, new ClassIsFinal());
    }

    public function assertClassIsAbstract(string $className) {
        self::assertThat($className, new ClassIsAbstract());
    }

    public function assertClassExtendsParent(string $parent, string $class) {
        self::assertThat($class, new ClassHasParent($parent));
    }

    // PROPERTY ASSERTIONS
    // =================================================================================================================
    // Assertion that can only be run against class or trait properties.

    public function assertPropertyIsPublic(string $property, string $className) {
        self::assertThat($property, new PropertyIsPublic($className));
    }

    public function assertPropertyIsProtected(string $property, string $className) {
        self::assertThat($property, new PropertyIsProtected($className));
    }

    public function assertPropertyIsPrivate(string $property, string $className) {
        self::assertThat($property, new PropertyIsPrivate($className));
    }

    public function assertPropertyHasDefaultValue($expected, string $className, string $propertyName) {
        self::assertThat($expected, new PropertyHasDefaultValue($className, $propertyName));
    }

    public function assertPropertyIsStatic(string $propertyName, string $className) {
        self::assertThat($propertyName, new PropertyIsStatic($className));
    }

    public function assertPropertyHasDocComment(string $docComment, string $className, string $propertyName) {
        self::assertThat($docComment, new PropertyHasDocComment($className, $propertyName));
    }

    // METHOD ASSERTIONS
    // =================================================================================================================
    // Assertion that can only be run against interface, class or trait methods.

    public function assertMethodIsPublic(string $methodName, string $className) {
        self::assertThat($methodName, new MethodIsPublic($className));
    }

    public function assertMethodIsProtected(string $methodName, string $className) {
        self::assertThat($methodName, new MethodIsProtected($className));
    }

    public function assertMethodIsPrivate(string $methodName, string $className) {
        self::assertThat($methodName, new MethodIsPrivate($className));
    }

    public function assertMethodIsStatic(string $methodName, string $className) {
        self::assertThat($methodName, new MethodIsStatic($className));
    }

    public function assertMethodIsFinal(string $methodName, string $className) {
        self::assertThat($methodName, new MethodIsFinal($className));
    }

    public function assertMethodHasReturnType(string $type, string $className, string $methodName) {
        self::assertThat($type, new MethodHasReturnType($className, $methodName));
    }

    public function assertMethodHasDocComment(string $docComment, string $className, string $methodName) {
        self::assertThat($docComment, new MethodHasDocComment($className, $methodName));
    }

    public function assertMethodParameterIsPresent(int $parameterIndex, string $className, string $methodName) {
        self::assertThat($parameterIndex, new MethodParameterIsPresent($className, $methodName));
    }

    public function assertMethodParameterIsRequired(int $parameterIndex, string $className, string $methodName) {
        self::assertThat($parameterIndex, new MethodParameterIsRequired($className, $methodName));
    }

    public function assertMethodParameterIsVariadic(int $parameterIndex, string $className, string $methodName) {
        self::assertThat($parameterIndex, new MethodParameterIsVariadic($className, $methodName));
    }

    public function assertMethodParameterIsPassedByReference(int $parameterIndex, string $className, string $methodName) {
        self::assertThat($parameterIndex, new MethodParameterIsByReference($className, $methodName));
    }

    public function assertMethodParameterHasDefaultValue($expected, string $className, string $methodName, int $paramIndex) {
        self::assertThat($expected, new MethodParameterHasDefaultValue($className, $methodName, $paramIndex));
    }

    public function assertMethodParameterHasTypeDeclaration(string $type, string $className, string $methodName, int $paramIndex) {
        self::assertThat($type, new MethodParameterHasTypeDeclaration($className, $methodName, $paramIndex));
    }

}

