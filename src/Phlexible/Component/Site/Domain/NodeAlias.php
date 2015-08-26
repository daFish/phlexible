<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Domain;

/**
 * Node alias
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeAlias
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $nodeId;

    /**
     * @var string
     */
    private $language;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $nodeId
     * @param string $language
     */
    public function __construct($name, $nodeId, $language)
    {
        $this->name = $name;
        $this->nodeId = $nodeId;
        $this->language = $language;
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
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }
}