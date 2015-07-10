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
use Phlexible\Bundle\TreeBundle\Entity\RedirectRoute;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * URL generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteGenerator implements RouteGeneratorInterface
{
    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;

    /**
     * @param PathGeneratorInterface $pathGenerator
     */
    public function __construct(PathGeneratorInterface $pathGenerator)
    {
        $this->pathGenerator = $pathGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNodeRoute(NodeContext $node, Siteroot $siteroot, $language)
    {
        $route = new Route(
            $this->pathGenerator->generatePath($node, $language),
            array(
                'nodeId' => $node->getId(),
                'siterootId' => $siteroot->getId(),
                'locale' => $language
            ),
            array(),
            array(),
            $siteroot->getDefaultUrl($language)->getHostname(),
            array('HTTP'),
            array('GET', 'POST')
        );

        $route->setName($node->getId());
        $route->setLanguage($language);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function generateEntryPointRoute(NodeContext $node, Url $url, $language)
    {
        $route = new RedirectRoute(
            '/',
            array(
                'nodeId' => $node->getId(),
                'siterootId' => $url->getSiteroot()->getId(),
                'locale' => $language
            ),
            array(),
            array(),
            $url->getHostname(),
            array('HTTP'),
            array('GET', 'POST')
        );

        $route->setName("entrypoint_{$url->getId()}");
        $route->setRouteName($node->getId());
        $route->setLanguage($language);

        return $route;
    }
}
