<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineContextEvent;
use Phlexible\Bundle\TreeBundle\Model\NodeChangeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\RouteManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\RouteGenerator\LanguagePathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\NodeIdPathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\PathGenerator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\RouteGenerator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\SuffixPathDecorator;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Node listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeListener implements EventSubscriberInterface
{
    /**
     * @var RouteManagerInterface
     */
    private $routeManager;

    /**
     * @var \Phlexible\Component\Site\Model\SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var NodeChangeManagerInterface
     */
    private $historyManager;

    /**
     * @param RouteManagerInterface                                $routeManager
     * @param \Phlexible\Component\Site\Model\SiteManagerInterface $siterootManager
     * @param NodeChangeManagerInterface                           $historyManager
     */
    public function __construct(
        RouteManagerInterface $routeManager,
        SiteManagerInterface $siterootManager,
        NodeChangeManagerInterface $historyManager
    ) {
        $this->routeManager = $routeManager;
        $this->siterootManager = $siterootManager;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::CREATE_NODE_CONTEXT => 'onCreateNode',
            TreeEvents::CREATE_NODE_INSTANCE_CONTEXT => 'onCreateNodeInstance',
            TreeEvents::PUBLISH_NODE_CONTEXT => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE_CONTEXT => 'onSetNodeOffline',
            TreeEvents::MOVE_NODE_CONTEXT => 'onMoveNode',
            TreeEvents::DELETE_NODE_CONTEXT => 'onDeleteNode',
        );
    }

    /**
     * @param NodeContextEvent $event
     */
    public function onCreateNode(NodeContextEvent $event)
    {
        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'createNode',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param NodeContextEvent $event
     */
    public function onCreateInstanceNode(NodeContextEvent $event)
    {
        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'createNodeInstance',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param PublishNodeContextEvent $event
     */
    public function onPublishNode(PublishNodeContextEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        $newRoute = $this->generateRoute($node, $language);

        $route = $this->routeManager->findOneBy(array('name' => $node->getId(), 'language' => $language));

        if (!$route) {
            $route = $newRoute;
        } else {
            $route->setHost($newRoute->getHost());
            $route->setOptions($newRoute->getOptions());
            $route->setCondition($newRoute->getCondition());
            $route->setDefaults($newRoute->getDefaults());
            $route->setMethods($newRoute->getDefaults());
            $route->setName($newRoute->getName());
            $route->setPath($newRoute->getPath());
            $route->setRequirements($newRoute->getRequirements());
            $route->setSchemes($newRoute->getSchemes());
        }

        $this->routeManager->updateRoute($route);

        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'publishNode',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param SetNodeOfflineContextEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineContextEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        // TODO: history?
        foreach ($this->routeManager->findBy(array('name' => $node->getId(), 'language' => $language)) as $route) {
            $this->routeManager->deleteRoute($route);
        }

        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'setNodeOffline',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param MoveNodeContextEvent $event
     */
    public function onMoveNode(MoveNodeContextEvent $event)
    {
        $node = $event->getNode();

        foreach ($node->getTree()->getPublishedLanguages($node) as $language) {
            $newRoute = $this->generateRoute($node, $language);

            $route = $this->routeManager->findOneBy(array('name' => $node->getId(), 'language' => $language));

            if (!$route) {
                $route = $newRoute;
            } else {
                $route->setHost($newRoute->getHost());
                $route->setOptions($newRoute->getOptions());
                $route->setCondition($newRoute->getCondition());
                $route->setDefaults($newRoute->getDefaults());
                $route->setMethods($newRoute->getDefaults());
                $route->setName($newRoute->getName());
                $route->setPath($newRoute->getPath());
                $route->setRequirements($newRoute->getRequirements());
                $route->setSchemes($newRoute->getSchemes());
            }

            $this->routeManager->updateRoute($route);
        }

        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'moveNode',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param NodeContextEvent $event
     */
    public function onDeleteNode(NodeContextEvent $event)
    {
        $node = $event->getNode()->getNode();

        $this->historyManager->insert(
            'deleteNode',
            $node->getContentId(),
            $node->getCreateUserId(),
            $node->getId(),
            null,
            null,
            null,
            null
        );
    }

    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return Route
     */
    private function generateRoute(NodeContext $node, $language)
    {
        $routeGenerator = new RouteGenerator(
            new PathGenerator(
                array(
                    new LanguagePathDecorator(),
                    new NodeIdPathDecorator(),
                    new SuffixPathDecorator(),
                )
            )
        );

        $siteroot = $this->siterootManager->find($node->getTree()->getSiterootId());

        return $routeGenerator->generateNodeRoute($node, $siteroot, $language);
    }
}
