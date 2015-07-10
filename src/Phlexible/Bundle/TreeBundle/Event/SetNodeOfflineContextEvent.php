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
 * Set node offline context event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetNodeOfflineContextEvent extends NodeContextEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param NodeContext $node
     * @param string      $language
     */
    public function __construct(NodeContext $node, $language)
    {
        parent::__construct($node);

        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
