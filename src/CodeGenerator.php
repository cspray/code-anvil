<?php

declare(strict_types=1);

namespace Cspray\CodeAnvil;

use Cspray\CodeAnvil\Info\{
    Info,
    ClassInfo,
    InterfaceInfo,
    TraitInfo,
    MethodInfo
};
use Ardent\Collection\LinkedList;

class CodeGenerator {

    private $nativeTypeDeclarations = [
        'string', 'int', 'bool', 'array', 'self', 'parent', 'callable', 'float'
    ];

    public function generate(Info $info) : string {
        $lines = new LinkedList();
        $lines->push('<?php');
        $lines->push('');

        if ($info->isDeclaredStrict()) {
            $lines->push('declare(strict_types=1);');
            $lines->push('');
        }

        if ($info instanceof ClassInfo) {
            return $this->generateClass($info, $lines);
        } elseif ($info instanceof InterfaceInfo) {
            return $this->generateInterface($info, $lines);
        } elseif ($info instanceof TraitInfo) {
            return $this->generateTrait($info, $lines);
        } else {
            $msg = sprintf('Generating %s is not supported at this time.', get_class($info));
            throw new \InvalidArgumentException($msg);
        }
    }

    private function generateClass(ClassInfo $info, LinkedList $lines) : string {
        if ($this->addNamespace($info, $lines)) { $lines->push(''); }
        if ($this->addUses($info, $lines)) { $lines->push(''); }

        $this->addDocComment($info, $lines);
        $this->addTypeSignature($info, $lines);
        $lines->push('');

        if ($this->addConstants($info, $lines)) { $lines->push(''); }
        if ($this->addProperties($info, $lines)) { $lines->push(''); }

        $this->addMethods($info, $lines);

        $lines->push('}');
        return implode("\n", iterator_to_array($lines));
    }

    private function generateInterface(InterfaceInfo $info, LinkedList $lines) : string {
        if ($this->addNamespace($info, $lines)) { $lines->push(''); }
        if ($this->addUses($info, $lines)) { $lines->push(''); }

        $this->addTypeSignature($info, $lines);
        $lines->push('');
        if ($this->addConstants($info, $lines)) { $lines->push(''); }
        $this->addMethods($info, $lines);
        $lines->push('}');

        return implode("\n", iterator_to_array($lines));
    }

    private function generateTrait(TraitInfo $info, LinkedList $lines) : string {
        if ($this->addNamespace($info, $lines)) { $lines->push(''); }
        if ($this->addUses($info, $lines)) { $lines->push(''); }

        $this->addDocComment($info, $lines);
        $this->addTypeSignature($info, $lines);
        $lines->push('');

        if ($this->addProperties($info, $lines)) { $lines->push(''); }
        $this->addMethods($info, $lines);

        $lines->push('}');
        return implode("\n", iterator_to_array($lines));
    }

    private function addNamespace(Info $info, LinkedList $lines) : bool {
        if ($ns = $info->getNamespace()) {
            $lines->push("namespace $ns;");
            return true;
        }

        return false;
    }

    private function addUses(Info $info, LinkedList $lines) : bool {
        $origSize = $lines->count();

        $uses = [];

        if ($info instanceof ClassInfo) {
            $this->addExtendedUses($info, $uses);
        }

        $this->addInterfaceUses($info, $uses);
        $this->addMethodUses($info, $uses);

        foreach ($uses as $name => $alias) {
            if (is_null($alias)) {
                $lines->push("use {$name};");
            } else {
                $lines->push("use {$name} as {$alias};");
            }
        }

        return $origSize < $lines->count();
    }

    private function addExtendedUses(ClassInfo $info, array &$uses) {
        $parentClass = $info->getParentClass();
        if ($parentClass) {
            if (!$parentAlias = $info->getParentClassAlias()) {
                $parentAlias = $this->getShortClassName($parentClass);
            }

            if ($parentClass === $parentAlias || $parentAlias === $this->getShortClassName($parentClass)) {
                $uses[$parentClass] = null;
            } else {
                $uses[$parentClass] = $parentAlias;
            }
        }
    }

    private function addInterfaceUses(Info $info, array &$uses) {
        if (!$info instanceof TraitInfo) {
            $interfaces = ($info instanceof ClassInfo) ? $info->getImplementedInterfaces() : $info->getExtendedInterfaces();
            foreach ($interfaces as $interface) {
                $this->setClassAlias($interface);
                $this->setUses($uses, $interface);
            }
        }
    }

    private function addMethodUses(Info $info, array &$uses) {
        foreach ($info->getMethods() as $method) {
            $this->addMethodParameterUses($info, $method, $uses);
            $this->addMethodReturnTypeUses($info, $method, $uses);
        }
    }

    private function addMethodParameterUses(Info $class, MethodInfo $method, array &$uses) {
        $class = $class->getName();
        foreach ($method->getParameters() as $param) {
            if ($param->hasTypeDeclaration()) {
                $type = $param->getTypeDeclaration();
                if (!in_array($type['name'], $this->nativeTypeDeclarations) && $type['name'] !== $class) {
                    $this->setClassAlias($type);
                    $this->setUses($uses, $type);
                }
            }
        }
    }

    private function addMethodReturnTypeUses(Info $class, MethodInfo $method, array &$uses) {
        $class = $class->getName();
        if ($method->hasReturnType()) {
            $type = $method->getReturnType();
            if (!in_array($type['name'], $this->nativeTypeDeclarations) && $type['name'] !== $class) {
                $this->setClassAlias($type);
                $this->setUses($uses, $type);
            }
        }
    }

    private function addDocComment($info, LinkedList $lines, $tabify = false) {
        if ($info->hasDocComment()) {
            $comment = $info->getDocComment();
            if ($tabify) {
                $comment = "\t" . str_replace("\n", "\n\t", $comment);
            }

            $lines->push($comment);
        }
    }

    private function addTypeSignature(Info $info, LinkedList $lines) {
        if ($info instanceof ClassInfo) {
            $classSig = $this->generateClassName($info);
            if ($pc = $info->getParentClass()) {
                if (!($pa = $info->getParentClassAlias())) {
                    $classSig .= " extends " . $this->getShortClassName($pc);
                } else {
                    $classSig .= " extends $pa";
                }
            }
        } elseif ($info instanceof InterfaceInfo) {
            $classSig = 'interface ' . $info->getName();
        } elseif ($info instanceof TraitInfo) {
            $classSig = 'trait ' . $info->getName();
        }

        if (!$info instanceof TraitInfo) {
            $interfaces = null;
            $collection = ($info instanceof ClassInfo) ? $info->getImplementedInterfaces() : $info->getExtendedInterfaces();
            foreach ($collection as $entry) {
                $this->setClassAlias($entry);
                $interfaces .= "{$entry['alias']}, ";
            }

            if ($interfaces) {
                $extendsOrImplements = ($info instanceof ClassInfo) ? ' implements ' : ' extends ';
                $classSig .= $extendsOrImplements . trim($interfaces, ', ');
            }
        }


        $lines->push($classSig . ' {');
    }

    private function generateClassName(ClassInfo $info) : string {
        $str = 'class';
        if ($info->isFinal()) {
            $str = 'final ' . $str;
        }

        if ($info->isAbstract()) {
            $str = 'abstract ' . $str;
        }

        $name = $info->getName();
        return "${str} ${name}";
    }

    private function addConstants(Info $info, LinkedList $lines) : bool {
        $constants = $info->getConstants();
        foreach ($constants as $const) {
            $name = $const->getName();
            $val = $this->varExport($const->getDefaultValue());
            $lines->push("\tconst {$name} = {$val};");
        }

        return !empty($constants);
    }

    private function addProperties(Info $info, LinkedList $lines) : bool {
        $properties = $info->getProperties();
        foreach ($properties as $prop) {
            $name = $prop->getName();
            $visibility = $prop->getVisibility();
            $static = $prop->isStatic() ? ' static' : '';

            $line = "\t{$visibility}{$static} \${$name}";
            if ($prop->hasDefaultValue()) {
                $line .= ' = ' . $this->varExport($prop->getDefaultValue());
            }
            $line .= ';';

            $this->addDocComment($prop, $lines, true);
            $lines->push($line);
        }

        return !empty($properties);
    }

    private function addMethods(Info $info, LinkedList $lines) : bool {
        $methods = $info->getMethods();
        foreach ($methods as $method) {
            $name = $method->getName();
            $visibility = $method->getVisibility();
            $static = $method->isStatic() ? 'static ' : '';
            $final = $method->isFinal() ? 'final ' : '';
            $body = $method->getBody();
            $params = $this->getParameters($method);

            $methodSig = "{$final}{$static}{$visibility} function {$name}({$params})";
            if ($method->hasReturnType()) {
                $rt = $method->getReturnType();
                $this->setClassAlias($rt);

                $methodSig .= ' : ' . $rt['alias'];
            }

            $this->addDocComment($method, $lines, true);
            if ($info instanceof InterfaceInfo) {
                $lines->push("\t{$methodSig};");
                $lines->push('');
            } else {
                $lines->push("\t{$methodSig} {");
                $lines->push("\t\t{$body}");
                $lines->push("\t" . '}');
                $lines->push('');
            }

        }

        return !empty($methods);
    }

    private function getParameters(MethodInfo $methodInfo) {
        $params = [];
        foreach ($methodInfo->getParameters() as $param) {
            $p = '$' . $param->getName();
            if ($param->isByReference()) {
                $p = '&' . $p;
            }

            if ($param->isVariadic()) {
                $p = '...' . $p;
            }

            if ($param->hasTypeDeclaration()) {
                $type = $param->getTypeDeclaration();
                $this->setClassAlias($type);
                $p = $type['alias'] . ' ' . $p;
            }

            if ($param->hasDefaultValue()) {
                $p .= ' = ' . $this->varExport($param->getDefaultValue());
            }

            $params[] = $p;
        }

        return implode(', ', $params);
    }

    private function getShortClassName($namespacedName) {
        $f = explode('\\', $namespacedName);
        return array_pop($f);
    }

    private function setClassAlias(array &$nameAlias) {
        if (!$nameAlias['alias']) {
            $nameAlias['alias'] = $this->getShortClassName($nameAlias['name']);
        }
    }

    private function setUses(array &$uses, array $nameAlias) {
        if ($nameAlias['name'] === $nameAlias['alias'] || $nameAlias['alias'] === $this->getShortClassName($nameAlias['name'])) {
            $uses[$nameAlias['name']] = null;
        } else {
            $uses[$nameAlias['name']] = $nameAlias['alias'];
        }
    }

    private function varExport($mixed) {
        $val = var_export($mixed, true);
        if (is_null($mixed) || is_bool($mixed)) {
            return strtolower($val);
        } elseif (is_array($mixed)) {
            return preg_replace("#\n#", '', $val);
        }

        return $val;
    }

}
