<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy\Generator;

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
