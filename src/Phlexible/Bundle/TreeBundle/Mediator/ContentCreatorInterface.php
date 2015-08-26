<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Content creator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentCreatorInterface extends MediatorInterface
{
    /**
     * @param mixed $contentDocument
     *
     * @return NodeInterface
     */
    public function createNodeForContentDocument($contentDocument);
}
