<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpParameter;
use CG\Generator\PhpProperty;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Php class generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhpClassGenerator
{
    const NS_PREFIX = 'Phlexible\\Element\\__CG__\\';

    /**
     * @var Distiller
     */
    private $distiller;

    /**
     * @var PhpClassMap
     */
    private $classMap;

    /**
     * @var PhpClassWriter
     */
    private $writer;

    /**
     * @param Distiller      $distiller
     * @param PhpClassWriter $writer
     */
    public function __construct(Distiller $distiller, PhpClassWriter $writer)
    {
        $this->distiller = $distiller;
        $this->writer = $writer;
    }

    /**
     * @return mixed
     */
    public function getManagerFile()
    {
        return $this->writer->getManagerFile();
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype[] $elementtypes
     *
     * @return string
     */
    public function generate(array $elementtypes)
    {
        $this->classMap = new PhpClassMap();

        foreach ($elementtypes as $elementtype) {
            if ($elementtype->getType() !== 'full' && $elementtype->getType() !== 'part' && $elementtype->getType() !== 'structure') {
                continue;
            }

            $data = $this->distiller->distill($elementtype);

            $classname = $this->normalizeName($elementtype->getTitle());
            $namespace = self::NS_PREFIX . $classname;
            $fqName = $namespace . '\\' . $classname;

            $class = $this->generateMainClass($fqName, $data, $elementtype);
            $this->generateInlineClasses($namespace, $class, $data);
            $this->generateReferenceClasses(self::NS_PREFIX . 'Reference', $class, $data);

            $class
                ->setConstant('ELEMENTTYPE_ID', $elementtype->getId())
                ->setConstant('ELEMENTTYPE_REVISION', $elementtype->getRevision())
                ->setConstant('ELEMENTTYPE_NAME', $elementtype->getUniqueId())
                ->setAttribute('elementtypeId', $elementtype->getId());
        }

        foreach ($this->classMap->all() as $class) {
            $getChildrenParts = $class->getAttributeOrElse('__getChildren', array());
            if (count($getChildrenParts)) {
                $class->getMethod('__getChildren')
                    ->setBody("return array(\n".implode(',' . PHP_EOL, (array) $getChildrenParts)."\n);");
            }

            $setChildrenParts = $class->getAttributeOrElse('__setChildren', array());
            if (count($getChildrenParts)) {
                $class->getMethod('__setChildren')
                    ->setBody("foreach (\$children as \$name => \$nameChildren) {\n    foreach (\$nameChildren as \$child) {\n".implode(PHP_EOL, (array) $setChildrenParts)."\n    }\n}");
            }
        }

        return $this->writer->write($this->classMap);
    }

    /**
     * @param $name
     *
     * @return string
     */
    private function normalizeName($name)
    {
        return $this->toCamelCase(str_replace(' ', '', ucfirst(strtolower($name))));
    }

    /**
     * @param string      $className
     * @param array       $data
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return PhpClass
     */
    private function generateMainClass($className, array $data, Elementtype $elementtype)
    {
        $constr = PhpMethod::create('__construct')
            ->addParameter(PhpParameter::create('id')
                ->setDefaultValue(null))
            ->addParameter(PhpParameter::create('version')
                ->setDefaultValue(null))
            ->setBody("\$this->__id = \$id;\n\$this->__version = \$version;\n")
            ->setDocblock("/**\n * @param string \$id\n * @param int \$version\n */\n");

        $idProperty = PhpProperty::create('__id')
            ->setVisibility('private')
            ->setDocblock("\n/** @var string */\n");

        $versionProperty = PhpProperty::create('__version')
            ->setVisibility('private')
            ->setDocblock("\n/** @var int */\n");

        $idGetter = PhpMethod::create('__id')
            ->setBody("return \$this->__id;\n")
            ->setDocblock("/**\n * @return string\n */\n");

        $versionGetter = PhpMethod::create('__version')
            ->setBody("return \$this->__version;\n")
            ->setDocblock("/**\n * @return int\n */\n");

        $elementtypeIdGetter = PhpMethod::create('__elementtypeId')
            ->setBody("return '{$elementtype->getId()}';\n")
            ->setDocblock("/**\n * @return string\n */\n");

        $elementtypeRevisionGetter = PhpMethod::create('__elementtypeRevision')
            ->setBody("return '{$elementtype->getRevision()}';\n")
            ->setDocblock("/**\n * @return int\n */\n");

        $elementtypeNameGetter = PhpMethod::create('__elementtypeName')
            ->setBody("return '{$elementtype->getUniqueId()}';\n")
            ->setDocblock("/**\n * @return int\n */\n");

        $toArray = PhpMethod::create('__toArray')
            ->setBody("\$children = array();\nforeach (\$this->__getChildren() as \$name => \$nameChildren) {\n    foreach (\$nameChildren as \$child) {\n        \$childData = \$child->__toArray();\n        \$childData['parent'] = \$name;\n        \$children[] = \$childData;\n    }\n}\nreturn array(\n    'id' => \$this->__id(),\n    'version' => \$this->__version(),\n    'values' => \$this->__getValues(),\n    'children' => \$children\n);")
            ->setDocblock("/**\n * @return \\Phlexible\\Bundle\\ElementBundle\\Proxy\\ChildStructureInterface[]\n */\n");

        $class = $this->generateClass($className, $data)
            ->addInterfaceName('Phlexible\Bundle\ElementBundle\Proxy\MainStructureInterface')
            ->setProperty($idProperty)
            ->setProperty($versionProperty)
            ->setMethod($constr)
            ->setMethod($idGetter)
            ->setMethod($versionGetter)
            ->setMethod($elementtypeIdGetter)
            ->setMethod($elementtypeRevisionGetter)
            ->setMethod($elementtypeNameGetter)
            ->setMethod($toArray);

        return $class;
    }

    /**
     * @param string                   $className
     * @param array                    $data
     * @param ElementtypeStructureNode $node
     *
     * @return PhpClass
     */
    private function generateChildClass($className, array $data, ElementtypeStructureNode $node)
    {
        $constr = PhpMethod::create('__construct')
            ->addParameter(PhpParameter::create('id')
                ->setDefaultValue(null))
            ->setBody("\$this->__id = \$id;\n")
            ->setDocblock("/**\n * @param string \$id\n */\n");

        $idProperty = PhpProperty::create('__id')
            ->setVisibility('private')
            ->setDocblock("\n/** @var string */\n");

        $idGetter = PhpMethod::create('__id')
            ->setBody("return \$this->__id;\n")
            ->setDocblock("/**\n * @return string\n */\n");

        $nameGetter = PhpMethod::create('__name')
            ->setBody("return \$this->__name;\n")
            ->setDocblock("/**\n * @return string\n */\n");

        $dsIdGetter = PhpMethod::create('__dsId')
            ->setBody("return '{$node->getDsId()}';\n")
            ->setDocblock("/**\n * @return string\n */\n");

        $toArray = PhpMethod::create('__toArray')
            ->setBody("\$children = array();\nforeach (\$this->__getChildren() as \$name => \$nameChildren) {\n    foreach (\$nameChildren as \$child) {\n        \$childData = \$child->__toArray();\n        \$childData['parent'] = \$name;\n        \$children[] = \$childData;\n    }\n}\nreturn array(\n    'id' => \$this->__id(),\n    'dsId' => \$this->__dsId(),\n    'parent' => null,\n    'values' => \$this->__getValues(),\n    'children' => \$children\n);")
            ->setDocblock("/**\n * @return \\Phlexible\\Bundle\\ElementBundle\\Proxy\\ChildStructureInterface[]\n */\n");

        $class = $this->generateClass($className, $data)
            ->addInterfaceName('Phlexible\Bundle\ElementBundle\Proxy\ChildStructureInterface')
            ->setAttribute('dsId', $node->getDsId())
            ->addInterfaceName('Phlexible\Bundle\ElementBundle\Proxy\ChildStructureInterface')
            ->setProperty($idProperty)
            ->setMethod($constr)
            ->setMethod($dsIdGetter)
            ->setMethod($idGetter)
            ->setMethod($nameGetter)
            ->setMethod($toArray)
            ->setConstant('DS_ID', $node->getDsId());

        $class->setMethod(PhpMethod::create('__name')->setBody("return '{$node->getName()}';"));

        return $class;
    }

    /**
     * @param string $namespace
     * @param string $section
     *
     * @return PhpClass
     */
    private function generateChildInterface($namespace, $section)
    {
        $className = $namespace . ucfirst($section) . 'Interface';
        $className = $namespace . 'Interface';

        if ($this->classMap->has($className)) {
            return $this->classMap->get($className);
        }

        $class = PhpClass::create($className)
            ->setAttribute('interface', 1)
            ->setDocblock("/**\n * DO NOT EDIT THIS FILE - IT WAS CREATED BY PHLEXIBLE'S PROXY GENERATOR\n * " . get_class($this) . "\n */\n");

        $this->classMap->add($className, $class);

        return $class;
    }

    /**
     * @param string $className
     * @param array  $data
     *
     * @return PhpClass
     */
    private function generateClass($className, array $data)
    {
        $items = array();
        foreach ($data as $item) {
            if (!isset($item['children'])) {
                $items[] = $item;
            }
        }

        $class = PhpClass::create($className)
            ->setMethod(PhpMethod::create('__construct'))
            ->setFinal(true)
            ->setDocblock("/**\n * DO NOT EDIT THIS FILE - IT WAS CREATED BY PHLEXIBLE'S PROXY GENERATOR\n * " . get_class($this) . "\n */\n")
            ->setAttribute('__getChildren', new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS))
            ->setAttribute('__setChildren', new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS));

        $setValueParts = [];
        $getValueParts = [];
        $getValueDescriptorParts = [];

        foreach ($items as $item) {
            $node = $item['node'];
            $field = $item['field'];
            $nodeType = $node->getType();
            $dataType = $field->getDataType();

            $valueName = lcfirst($this->toCamelCase($node->getName()));
            $nodeName = ucfirst($valueName);
            $getterName = 'get' . $nodeName;
            $setterName = 'set' . $nodeName;

            $value = PhpProperty::create(lcfirst($valueName))
                ->setVisibility('private')
                ->setDocblock("\n/** @var $dataType */\n");
            $class->setProperty($value);

            $getter = PhpMethod::create($getterName)
                ->setDocblock("/**\n * @return $dataType\n */")
                ->setBody("return \$this->$valueName;");
            $class->setMethod($getter);

            $setterValue = PhpParameter::create($valueName);
            $setter = PhpMethod::create($setterName)
                ->setDocblock("/**\n * @param $dataType \$$valueName\n *\n * @return \$this\n */")
                ->setBody("\$this->$valueName = \$$valueName;\n\nreturn \$this;")
                ->addParameter($setterValue);
            $class->setMethod($setter);

            $setValueParts[] = "    if (\$dsId === '{$node->getDsId()}') {\n        \$this->$setterName(\$value);\n        continue;\n    }";
            $getValueParts[] = "    '{$node->getDsId()}' => \$this->$getterName(),";
            $getValueDescriptorParts[] = "    '{$node->getDsId()}' => array('name' => '{$node->getName()}', 'type' => '$nodeType', 'dataType' => '$dataType', 'value' => \$this->$getterName()),";
        }

        $setValues = PhpMethod::create('__setValues')
            ->addParameter(PhpParameter::create('values')->setType('array'))
            ->setBody("foreach (\$values as \$dsId => \$value) {\n".implode(PHP_EOL, $setValueParts)."\n}")
            ->setDocblock("/**\n * @param array \$values\n */\n");
        $class->setMethod($setValues);

        $getValues = PhpMethod::create('__getValues')
            ->setBody("return array(\n".implode(PHP_EOL, $getValueParts)."\n);")
            ->setDocblock("/**\n * @return array\n */\n");
        $class->setMethod($getValues);

        $getValueDescriptors = PhpMethod::create('__getValueDescriptors')
            ->setBody("return array(\n".implode(PHP_EOL, $getValueDescriptorParts)."\n);")
            ->setDocblock("/**\n * @return array\n */\n");
        $class->setMethod($getValueDescriptors);

        $getChildren = PhpMethod::create('__getChildren')
            ->setBody("return array();")
            ->setDocblock("/**\n * @return \\Phlexible\\Bundle\\ElementBundle\\Proxy\\ChildStructureInterface[]\n */\n");
        $class->setMethod($getChildren);

        $setChildren = PhpMethod::create('__setChildren')
            ->addParameter(PhpParameter::create('children')->setType('array'))
            ->setDocblock("/**\n * @param array \$children\n */\n");
        $class->setMethod($setChildren);

        $this->classMap->add($className, $class);

        return $class;
    }

    /**
     * @param string   $namespace
     * @param PhpClass $parentClass
     * @param array    $data
     */
    private function generateInlineClasses($namespace, PhpClass $parentClass, array $data)
    {
        $items = array();
        foreach ($data as $item) {
            if (!isset($item['children'])) {
                continue;
            }

            $node = $item['node'];
            /* @var $node ElementtypeStructureNode */

            if ($node->isReferenced()) {
                continue;
            }

            $items[] = $item;
        }

        if (!count($items)) {
            return;
        }

        $allowed = array();
        foreach ($items as $item) {
            $parent = $item['node']->getParentNode();
            if ($parent->getType() === 'referenceroot') {
                $parent = $parent->getParentNode();
                if ($parent->getType() === 'reference') {
                    $parent = $parent->getParentNode();
                }
            }
            $parentName = $this->normalizeName($parent->getName());

            $name = $this->normalizeName($item['node']->getName());
            $fqName = $namespace . '\\' . $parentClass->getShortName() . $parentName . $name;

            $class = $this->generateChildClass($fqName, $item['children'], $item['node']);

            $classes[$name] = $class;

            $lcName = lcfirst($name);
            $ucName = ucfirst($name);

            if ($item['node']->isRepeatable()) {
                $allowed[$parentName] = $fqName;

                $interface = $this->generateChildInterface($parentClass->getName(), $this->toCamelCase($parentName));
                $this->classMap->get($fqName)->addInterfaceName($interface->getName());
            } else {
                $setter = PhpMethod::create('set' . $ucName)
                    ->addParameter(PhpParameter::create($lcName)->setType($fqName)->setDefaultValue(null))
                    ->setDocblock("/**\n * @param mixed \$$lcName\n *\n * @return \$this\n */")
                    ->setBody("\$this->$lcName = \$$lcName;\n\nreturn \$this;");
                $parentClass->setMethod($setter);

                $getter = PhpMethod::create('get' . $ucName)
                    ->setDocblock("/**\n * @return \\$fqName|null\n */")
                    ->setBody("return \$this->$lcName;");
                $parentClass->setMethod($getter);

                // add children property to parent
                $property = PhpProperty::create($lcName)
                    ->setVisibility('private')
                    ->setDocblock("\n/** @var \\$fqName */\n");
                $parentClass->setProperty($property);

            }

            $this->generateInlineClasses($namespace, $class, $item['children']);
        }

        if (count($allowed)) {
            foreach ($allowed as $name => $allow) {
                $normalizedName = $this->normalizeName($name);
                $lcName = lcfirst($normalizedName);
                $ucName = ucfirst($normalizedName);

                $interface = $this->generateChildInterface($parentClass->getName(), $normalizedName);

                // add arraycollection import to parent
                $parentClass->addUseStatement('Doctrine\Common\Collections\ArrayCollection');

                // add children arraycollection initialization to parent
                $constr = $parentClass->getMethod('__construct');
                $constr->setBody($constr->getBody() . "\$this->{$lcName} = new ArrayCollection();\n");

                $adder = PhpMethod::create('add' . $ucName)
                    ->addParameter(PhpParameter::create($lcName)->setType($interface->getName()))
                    ->setDocblock("/**\n * @param mixed \$$lcName\n *\n * @return \$this\n */")
                    ->setBody("\$this->{$lcName}->add(\${$lcName});\n\nreturn \$this;");
                $parentClass->setMethod($adder);

                $getter = PhpMethod::create('get' . $ucName)
                    ->setDocblock("/**\n * @return ArrayCollection\n */")
                    ->setBody("return \$this->{$lcName};");
                $parentClass->setMethod($getter);

                // add children property to parent
                $property = PhpProperty::create($lcName)
                    ->setVisibility('private')
                    ->setDocblock("\n/** @var ArrayCollection */\n");
                $parentClass->setProperty($property);

                $parentClass->getAttribute('__getChildren')->append("    '{$name}' => \$this->{$lcName}->toArray()");
                $parentClass->getAttribute('__setChildren')->append("        if (\$name === '{$name}') {\n            \$this->{$lcName}->add(\$child);\n            continue;\n        }");
            }
        }
    }

    /**
     * @param string   $namespace
     * @param PhpClass $parentClass
     * @param array    $data
     */
    private function generateReferenceClasses($namespace, PhpClass $parentClass, array $data)
    {
        $items = array();
        foreach ($data as $item) {
            if (!isset($item['children'])) {
                continue;
            }

            $node = $item['node'];
            /* @var $node ElementtypeStructureNode */

            if (!$node->isReferenced()) {
                continue;
            }

            $items[] = $item;
        }

        if (!count($items)) {
            return;
        }

        $allowed = array();
        foreach ($items as $item) {
            $name = $this->normalizeName($item['node']->getName());
            $fqName = $namespace . '\\' . $name;

            $class = $this->generateChildClass($fqName, $item['children'], $item['node']);

            $classes[$name] = $class;

            $lcName = lcfirst($name);
            $ucName = ucfirst($name);

            if ($item['node']->isRepeatable()) {
                $parent = $item['node']->getParentNode();
                if ($parent->getType() === 'referenceroot') {
                    $parent = $parent->getParentNode();
                    if ($parent->getType() === 'reference') {
                        $parent = $parent->getParentNode();
                    }
                }
                $parentName = $parent->getName();
                $allowed[$parentName][] = $fqName;

                $interface = $this->generateChildInterface($parentClass->getName(), $this->toCamelCase($parentName));
                $this->classMap->get($fqName)->addInterfaceName($interface->getName());
            } else {
                $setter = PhpMethod::create('set' . $ucName)
                    ->addParameter(PhpParameter::create($lcName)->setType($fqName)->setDefaultValue(null))
                    ->setDocblock("/**\n * @param mixed \$$lcName\n *\n * @return \$this\n */")
                    ->setBody("\$this->$lcName = \$$lcName;\n\nreturn \$this;");
                $parentClass->setMethod($setter);

                $getter = PhpMethod::create('get' . $ucName)
                    ->setDocblock("/**\n * @return \\$fqName|null\n */")
                    ->setBody("return \$this->$lcName;");
                $parentClass->setMethod($getter);

                // add children property to parent
                $property = PhpProperty::create($lcName)
                    ->setVisibility('private')
                    ->setDocblock("\n/** @var \\$fqName */\n");
                $parentClass->setProperty($property);
            }

            $this->generateReferenceClasses($namespace, $class, $item['children']);
        }

        if (count($allowed)) {
            $getChildrenParts = array();
            $setChildrenParts = array();

            foreach ($allowed as $name => $allow) {
                $normalizedName = $this->normalizeName($name);
                $lcName = lcfirst($normalizedName);
                $ucName = ucfirst($normalizedName);

                $interface = $this->generateChildInterface($parentClass->getName(), $normalizedName);

                // add arraycollection import to parent
                $parentClass->addUseStatement('Doctrine\Common\Collections\ArrayCollection');

                // add children arraycollection initialization to parent
                $constr = $parentClass->getMethod('__construct');
                $constr->setBody($constr->getBody() . "\$this->{$lcName} = new ArrayCollection();\n");

                $adder = PhpMethod::create('add' . $ucName)
                    ->addParameter(PhpParameter::create($lcName)->setType($interface->getName()))
                    ->setDocblock("/**\n * @param \\{$interface->getName()} \${$lcName}\n *\n * @return \$this\n */")
                    ->setBody("\$this->{$lcName}->add(\${$lcName});\n\nreturn \$this;");
                $parentClass->setMethod($adder);

                $getter = PhpMethod::create('get' . $ucName)
                    ->setDocblock("/**\n * @return ArrayCollection\n */")
                    ->setBody("return \$this->{$lcName};");
                $parentClass->setMethod($getter);

                // add children property to parent
                $property = PhpProperty::create($lcName)
                    ->setVisibility('private')
                    ->setDocblock("\n/** @var ArrayCollection */\n");
                $parentClass->setProperty($property);

                $parentClass->getAttribute('__getChildren')->append("    '{$name}' => \$this->{$lcName}->toArray()");
                $parentClass->getAttribute('__setChildren')->append("        if (\$name === '{$name}') {\n            \$this->{$lcName}->add(\$child);\n            continue;\n        }");
            }
        }
    }

    /**
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    private function toCamelCase($str, $capitaliseFirstChar = true)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
}
