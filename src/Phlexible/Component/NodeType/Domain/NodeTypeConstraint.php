<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType\Domain;

use JMS\Serializer\Annotation as Serializer;

/**
 * Node type constraint
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="nodeConstraint")
 * @Serializer\ExclusionPolicy("all")
 */
class NodeTypeConstraint
{
    /**
     * @var string
     * @Serializer\Type(name="string")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $name;

    /**
     * @var bool
     * @Serializer\Type(name="boolean")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $allowed;

    /**
     * @var array
     * @Serializer\Type(name="array<string>")
     * @Serializer\Expose()
     */
    private $nodeTypes;

    /**
     * @param string $name
     * @param bool   $allowed
     * @param array  $nodeTypes
     */
    public function __construct($name, $allowed = true, $nodeTypes = array())
    {
        $this->name = $name;
        $this->allowed = $allowed;
        $this->nodeTypes = $nodeTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->allowed;
    }

    /**
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->nodeTypes;
    }
}
