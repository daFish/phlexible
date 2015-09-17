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
