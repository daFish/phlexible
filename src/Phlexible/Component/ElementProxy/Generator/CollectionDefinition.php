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
 * Collection definition.
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
