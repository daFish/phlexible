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

use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\Menu\MenuItem;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Tree\WorkingTreeContext;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Get menu listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetMenuListener
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param SiteManagerInterface      $siterootManager
     * @param TreeManagerInterface          $treeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SiteManagerInterface $siterootManager,
        TreeManagerInterface $treeManager,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param GetMenuEvent $event
     */
    public function onGetMenu(GetMenuEvent $event)
    {
        $items = $event->getItems();

        $treeContext = new WorkingTreeContext('de');

        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->treeManager->getBySiteRootId($treeContext, $siteroot->getId());
            $root = $tree->getRoot();

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->authorizationChecker->isGranted(array('permission' => 'VIEW'), $root)) {
                continue;
            }

            $menuItem = new MenuItem('tree', 'trees');
            $menuItem->setParameters(
                array(
                    'siterootId' => $siteroot->getId(),
                    'title'      => $siteroot->getTitle(),
                )
            );

            $items->set('siteroot_' . $siteroot->getId(), $menuItem);
        }
    }
}
