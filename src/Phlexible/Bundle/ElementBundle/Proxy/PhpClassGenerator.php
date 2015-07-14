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
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;

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
     * @param Elementtype[] $elementtypes
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

            $class = $this->generateClass($fqName, $data);
            $this->generateInlineClasses($namespace, $class, $data);
            $this->generateReferenceClasses(self::NS_PREFIX . 'Reference', $class, $data);

            $class->setConstant('ELEMENTTYPE_ID', $elementtype->getId());
            $class->setConstant('ELEMENTTYPE_REVISION', $elementtype->getRevision());
            $class->setConstant('ELEMENTTYPE_NAME', $elementtype->getUniqueId());
            $class->setAttribute('elementtypeId', $elementtype->getId());
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
            ->setDocblock("/**\n * DO NOT EDIT THIS FILE - IT WAS CREATED BY PHLEXIBLE'S PROXY GENERATOR\n * " . get_class($this) . "\n */\n");

        $setValue = PhpMethod::create('__setValue')
            ->addParameter(PhpParameter::create('dsId'))
            ->addParameter(PhpParameter::create('value'))
            ->setDocblock("/**\n * @param string \$dsId\n * @param mixed \$value\n */\n")
            ->setBody('throw new \\Exception("Unknown dsId $dsId");');
        $class->setMethod($setValue);

        foreach ($items as $item) {
            $node = $item['node'];
            $field = $item['field'];
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

            $setValue
                ->setBody("if (\$dsId === '{$node->getDsId()}') {\n    \$this->$setterName(\$value);\n    return;\n}\n".$setValue->getBody());
            $class->setConstant('DS_ID', $node->getDsId());
        }

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
            $name = $this->normalizeName($item['node']->getName());
            $fqName = $namespace . '\\' . $name;

            $class = $this->generateClass($fqName, $item['children']);
            $class->setAttribute('dsId', $item['node']->getDsId());
            $idProperty = PhpProperty::create('__id')
                ->setVisibility('private')
                ->setDocblock("\n/** @var string */\n");
            $class->setProperty($idProperty);
            $constr = PhpMethod::create('__construct')
                ->addParameter(PhpParameter::create('id')
                    ->setDefaultValue(null))
                ->setBody("\$this->__id = \$id;\n")
                ->setDocblock("/**\n * @param string \$id\n */\n");
            $class->setMethod($constr);
            $idGetter = PhpMethod::create('__getId')
                ->setBody("return \$this->__id;\n")
                ->setDocblock("/**\n * @return string\n */\n");
            $class->setMethod($idGetter);

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
                $name = $this->normalizeName($name);
                $lcName = lcfirst($name);
                $ucName = ucfirst($name);

                $interface = $this->generateChildInterface($parentClass->getName(), $this->toCamelCase($name));

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
            }
        }
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

            $class = $this->generateClass($fqName, $item['children']);
            $class->setAttribute('dsId', $item['node']->getDsId());
            $idProperty = PhpProperty::create('__id')
                ->setVisibility('private')
                ->setDocblock("\n/** @var string */\n");
            $class->setProperty($idProperty);
            $constr = PhpMethod::create('__construct')
                ->addParameter(PhpParameter::create('id')
                    ->setDefaultValue(null))
                ->setBody("\$this->__id = \$id;\n")
                ->setDocblock("/**\n * @param string \$id\n */\n");
            $class->setMethod($constr);
            $idGetter = PhpMethod::create('__getId')
                ->setBody("return \$this->__id;\n")
                ->setDocblock("/**\n * @return string\n */\n");
            $class->setMethod($idGetter);

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
            foreach ($allowed as $name => $allow) {
                $name = $this->normalizeName($name);
                $lcName = lcfirst($name);
                $ucName = ucfirst($name);

                $interface = $this->generateChildInterface($parentClass->getName(), $this->toCamelCase($name));

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
