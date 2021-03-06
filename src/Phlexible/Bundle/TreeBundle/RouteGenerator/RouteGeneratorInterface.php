<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

/**
 * Route generator interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteGeneratorInterface
{
    /**
     * @param NodeContext                           $node
     * @param \Phlexible\Component\Site\Domain\Site $siteroot
     * @param string                                $language
     *
     * @return Route
     */
    public function generateNodeRoute(NodeContext $node, Site $siteroot, $language);

    /**
     * @param NodeContext $node
     * @param string      $siterootId
     * @param string      $hostname
     * @param string      $name
     * @param string      $language
     *
     * @return Route
     */
    public function generateEntryPointRoute(NodeContext $node, $siterootId, $hostname, $name, $language);
}
