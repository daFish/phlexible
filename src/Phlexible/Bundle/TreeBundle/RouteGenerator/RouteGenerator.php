<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Entity\RedirectRoute;
use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

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
    public function generateNodeRoute(NodeContext $node, Site $siteroot, $language)
    {
        $route = new Route(
            $this->pathGenerator->generatePath($node, $language),
            array(
                'nodeId' => $node->getId(),
                'siterootId' => $siteroot->getId(),
                '_locale' => $language
            ),
            array(),
            array(),
            $siteroot->getHostname(),
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
    public function generateEntryPointRoute(NodeContext $node, $siterootId, $hostname, $name, $language)
    {
        $route = new RedirectRoute(
            '/',
            array(
                'nodeId' => $node->getId(),
                'siterootId' => $siterootId,
                '_locale' => $language
            ),
            array(),
            array(),
            $hostname,
            array('HTTP'),
            array('GET', 'POST')
        );

        $route->setName("entrypoint_$name");
        $route->setRouteName($node->getId());
        $route->setLanguage($language);

        return $route;
    }
}
