<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Configurator;

use Phlexible\Bundle\CmsBundle\Configurator\Configuration;
use Phlexible\Bundle\CmsBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\CmsBundle\Event\ConfigureEvent;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Template configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NavigationConfigurator implements ConfiguratorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        return;
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        /** @var Siteroot $siteroot */
        $siteroot = $request->attributes->get('siteroot');

        $navigations = array();

        foreach ($siteroot->getNavigations() as $siterootNavigation) {
            $startTid = $siterootNavigation->getStartTreeId();
            $currentTreeNode = $treeNode = $renderConfiguration->get('treeNode');
            if ($startTid) {
                $treeNode = $currentTreeNode->getTree()->get($startTid);
            }

            $navigations[$siterootNavigation->getTitle()] = new NodeContext(
                $treeNode,
                $currentTreeNode,
                $siterootNavigation->getMaxDepth()
            );
        }

        $renderConfiguration
            ->addFeature('navigation')
            ->setVariable('navigation', $navigations);

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(TreeEvents::CONFIGURE_NAVIGATION, $event);
    }
}
