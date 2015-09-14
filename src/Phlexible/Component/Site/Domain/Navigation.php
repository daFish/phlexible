<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Domain;

use JMS\Serializer\Annotation as Serializer;

/**
 * Navigation
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="navigation")
 * @Serializer\ExclusionPolicy("all")
 */
class Navigation
{
    /**
     * @var string
     * @Serializer\Type(name="string")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $name;

    /**
     * @var int
     * @Serializer\Type(name="integer")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $nodeId;

    /**
     * @var int
     * @Serializer\Type(name="integer")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $maxDepth;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $nodeId
     * @param int    $maxDepth
     */
    public function __construct($name, $nodeId, $maxDepth)
    {
        $this->name = $name;
        $this->nodeId = $nodeId;
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param int $nodeId
     *
     * @return $this
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }
}
