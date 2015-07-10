<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator\VersionStrategy;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Version strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VersionStrategyInterface
{
    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return Element
     */
    public function find(NodeContext $node, $language);
}
