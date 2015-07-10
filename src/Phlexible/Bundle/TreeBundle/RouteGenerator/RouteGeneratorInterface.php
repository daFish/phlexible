<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Route generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteGeneratorInterface
{
    /**
     * @param NodeContext $node
     * @param Siteroot    $siteroot
     * @param string      $language
     *
     * @return Route
     */
    public function generateNodeRoute(NodeContext $node, Siteroot $siteroot, $language);

    /**
     * @param NodeContext $node
     * @param Url         $url
     * @param string      $language
     *
     * @return Route
     */
    public function generateEntryPointRoute(NodeContext $node, Url $url, $language);
}
