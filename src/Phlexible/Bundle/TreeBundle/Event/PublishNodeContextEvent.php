<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Publish node context event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PublishNodeContextEvent extends NodeContextEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $version;

    /**
     * @var bool
     */
    private $isRecursive = false;

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param int         $version
     * @param bool        $isRecursive
     */
    public function __construct(NodeContext $node, $language, $version, $isRecursive = false)
    {
        parent::__construct($node);

        $this->language = $language;
        $this->version = $version;
        $this->isRecursive = (bool) $isRecursive;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isRecursive()
    {
        return $this->isRecursive;
    }
}
