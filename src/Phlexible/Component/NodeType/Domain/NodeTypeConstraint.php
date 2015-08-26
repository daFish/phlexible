<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType\Domain;

/**
 * Node type constraint
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeTypeConstraint
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $allowed;

    /**
     * @var array
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
