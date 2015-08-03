<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Class definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassDefinition
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
     * @var ValueDefinition[]
     */
    private $values;

    /**
     * @var array
     */
    private $children;

    /**
     * @var array
     */
    private $collections;

    /**
     * @param string            $classname
     * @param string            $namespace
     * @param ValueDefinition[] $values
     * @param array             $children
     * @param array             $collections
     */
    public function __construct($classname, $namespace, array $values, array $children, array $collections)
    {
        $this->classname = $classname;
        $this->namespace = $namespace;
        $this->values = $values;
        $this->children = $children;
        $this->collections = $collections;
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
     * @return ValueDefinition[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return StructureClassDefinition[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return CollectionDefinition[]
     */
    public function getCollections()
    {
        return $this->collections;
    }
}
