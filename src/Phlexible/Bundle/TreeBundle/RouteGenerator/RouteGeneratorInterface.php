<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

/**
 * Route generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteGeneratorInterface
{
    /**
     * @param NodeContext $node
     * @param \Phlexible\Component\Site\Domain\Site    $siteroot
     * @param string      $language
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
