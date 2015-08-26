<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Elementtype\Event\ElementtypeUsageEvent;
use Phlexible\Component\Elementtype\Usage\Usage;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Phlexible\Component\Tree\WorkingTreeContext;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var ElementManagerInterface
     */
    private $elementManager;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param ElementManagerInterface $elementManager
     * @param NodeManagerInterface    $nodeManager
     * @param TreeManagerInterface    $treeManager
     * @param TokenStorageInterface   $tokenStorage
     */
    public function __construct(ElementManagerInterface $elementManager, NodeManagerInterface $nodeManager, TreeManagerInterface $treeManager, TokenStorageInterface $tokenStorage)
    {
        $this->elementManager = $elementManager;
        $this->nodeManager = $nodeManager;
        $this->treeManager = $treeManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtypeId = $event->getElementtype()->getId();
        $language = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser()->getInterfaceLanguage('de') : 'en';

        $contentIds = array();
        foreach ($this->elementManager->findBy(array('elementtypeId' => $elementtypeId)) as $element) {
            $contentIds[] = $element->getEid();
        }

        $nodeIds = array();
        foreach ($this->nodeManager->findBy(array('contentType' => 'element', 'contentId' => $contentIds)) as $node) {
            $nodeIds[$node->getId()] = $node->getContentId();
        }

        foreach ($nodeIds as $nodeId => $contentId) {
            $tree = $this->treeManager->getByNodeId(new WorkingTreeContext('de'), $nodeId);
            $node = $tree->get($nodeId);

            $event->addUsage(
                new Usage(
                    $event->getElementtype()->getType() . ' element',
                    'element',
                    $node->getId(),
                    $node->getField('backend', $language),
                    $node->getId()
                )
            );
        }
    }
}
