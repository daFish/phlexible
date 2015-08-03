<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Structure class definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StructureClassDefinition extends ClassDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @param string            $classname
     * @param string            $namespace
     * @param ValueDefinition[] $values
     * @param array             $children
     * @param array             $collections
     * @param string            $name
     * @param string            $dsId
     */
    public function __construct($classname, $namespace, array $values, array $children, array $collections, $name, $dsId)
    {
        parent::__construct($classname, $namespace, $values, $children, $collections);

        $this->name = $name;
        $this->dsId = $dsId;
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
    public function getDsId()
    {
        return $this->dsId;
    }
}
