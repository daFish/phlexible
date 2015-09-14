<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
