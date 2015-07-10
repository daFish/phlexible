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
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Node configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeConfigurator implements ConfiguratorInterface
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
     * @var ElementService
     */
    private $elementService;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param EventDispatcherInterface      $dispatcher
     * @param LoggerInterface               $logger
     * @param ElementService                $elementService
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$request->attributes->has('node') || !$request->attributes->get('node') instanceof NodeContext) {
            return;
        }

        /* @var $node NodeContext */
        $node = $originalNode = $request->attributes->get('node');
        $tree = $node->getTree();

        /*
        $eid = $treeNode->getTypeId();

        if (0) {
            // || $renderRequest->getVersionStrategy() === 'latest')
            if (!$this->authorizationChecker->isGranted('VIEW', $treeNode)) {
                $this->logger->debug('403 Forbidden du to missing VIEW content right');

                throw new \Makeweb_Renderers_Exception('Forbidden', 403);
            }
        }

        if ($treeNode !== $originalTreeNode) {
            $this->logger->debug('Switching to TID ' . $treeNode->getId());

            $renderRequest->setTreeNode($treeNode);
            $renderRequest->setVersion($elementVersion->getVersion());
        }

        // if available use delegation for showing element somewhere else in navigation
        if ($request->attributes->has('delegateTreeId')) {
            $delegateTreeNode = $tree->getNode($request->attributes->get('delegateTreeId'));
        }
        */

        $version = -1;
        if (!$request->attributes->get('preview')) {
            $version = $tree->getPublishedVersion($node, $request->getLocale());

            if (!$version) {
                throw new NotFoundHttpException("Node not published.");
            }
        }

        $renderConfiguration
            ->addFeature('node')
            ->set('node', $node)
            ->setVariable('nodeContext', $node)
            ->addFeature('eid')
            ->set('eid', $node->getNode()->getTypeId())
            ->set('version', $version)
            ->set('language', $request->getLocale());

        if ($template = $tree->getTemplate($node)) {
            $renderConfiguration
                ->addFeature('template')
                ->setVariable('template', $template);
        }

        $renderConfiguration
            ->setVariable('siteroot', $request->attributes->get('siteroot'));

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(TreeEvents::CONFIGURE_TREE_NODE, $event);

        return true;
    }
}
