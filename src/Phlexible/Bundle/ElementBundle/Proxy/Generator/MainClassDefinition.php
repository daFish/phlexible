<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Main class definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MainClassDefinition extends ClassDefinition
{
    /**
     * @var string
     */
    private $elementtypeId;

    /**
     * @var int
     */
    private $elementtypeRevision;

    /**
     * @var string
     */
    private $elementtypeName;

    /**
     * @param string            $classname
     * @param string            $namespace
     * @param ValueDefinition[] $values
     * @param array             $children
     * @param array             $collections
     * @param string            $elementtypeId
     * @param int               $elementtypeRevision
     * @param string            $elementtypeName
     */
    public function __construct($classname, $namespace, array $values, array $children, array $collections, $elementtypeId, $elementtypeRevision, $elementtypeName)
    {
        parent::__construct($classname, $namespace, $values, $children, $collections);

        $this->elementtypeId = $elementtypeId;
        $this->elementtypeRevision = $elementtypeRevision;
        $this->elementtypeName = $elementtypeName;
    }

    /**
     * @return string
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @return int
     */
    public function getElementtypeRevision()
    {
        return $this->elementtypeRevision;
    }

    /**
     * @return string
     */
    public function getElementtypeName()
    {
        return $this->elementtypeName;
    }
}
