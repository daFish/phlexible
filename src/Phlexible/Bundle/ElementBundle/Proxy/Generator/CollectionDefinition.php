<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Collection definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CollectionDefinition implements \Countable
{
    /**
     * @var string
     */
    private $classname;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $rawName;

    /**
     * @var array
     */
    private $classes;

    /**
     * @param string $classname
     * @param string $namespace
     * @param string $name
     * @param string $rawName
     * @param array  $classes
     */
    public function __construct($classname, $namespace, $name, $rawName, array $classes)
    {
        $this->classname = $classname;
        $this->namespace = $namespace;
        $this->name = $name;
        $this->rawName = $rawName;
        $this->classes = $classes;
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUpperName()
    {
        return ucfirst($this->name);
    }

    /**
     * @return string
     */
    public function getRawName()
    {
        return $this->rawName;
    }

    /**
     * @return StructureClassDefinition[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->classes);
    }
}
